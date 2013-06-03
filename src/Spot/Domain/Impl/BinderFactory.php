<?php
namespace Spot\Domain\Impl;

use Spot\Reflect\Reflection;
use Spot\Code\CodeStorage;

class BinderFactory {
    private $gen,
            $reflection,
            $codeStorage;
    
    public function __construct(
            BinderGenerator $gen, 
            Reflection $reflection,
            CodeStorage $codeStorage) {
        $this->gen = $gen;
        $this->reflection = $reflection;
        $this->codeStorage = $codeStorage;
    }
    
    public function getBinder($className) {
        $binderName = 'Binder__'.md5($className);
        $fqcn = $this->codeStorage->load($binderName);
        if(empty($fqcn)) {
            $type = $this->reflection->getType($className);
            $code = $this->gen->generate($type);
            
            $fqcn = $this->codeStorage->store($binderName, $code);
        }
        
        return $fqcn;
    }
}