<?php
namespace Spot\App\Cli\Impl;

use Spot\Code\CodeStorage;
use Spot\Reflect\Reflection;
use Spot\Code\Impl\CodeWriterImpl;

class ActionFactory {
    private $reflection,
            $static,
            $storage;
    
    public function __construct(
            Reflection $reflection,
            StaticActionGenerator $static,
            CodeStorage $storage) {
        $this->reflection = $reflection;
        $this->static = $static;
        $this->storage = $storage;
    }
    
    public function create(array $method) {
        $className = 'CliAction__'.md5("$method[0]::$method[1]");
        $fqcn = $this->storage->load($className);
        if(empty($fqcn)) {
            $method = $this->getMethod($this->getType($method[0]), $method[1]);

            $code = $method->isStatic() 
                    ? $this->static->generate($method, $className)
                    : $this->instance->generate($method, $className);
            $code = $writer->getCode();
            
            $fqcn = $this->storage->store($className, $code);
        }
        
        return $fqcn;
    }
}