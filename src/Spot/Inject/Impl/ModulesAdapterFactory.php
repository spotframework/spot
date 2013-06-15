<?php
namespace Spot\Inject\Impl;

use Spot\Code\CodeStorage;

class ModulesAdapterFactory {
    private $builder,
            $singletons,
            $codeStorage;
    
    public function __construct(
            BindingBuilder $builder,
            SingletonPool $singletons,
            CodeStorage $codeStorage) {
        $this->builder = $builder;
        $this->singletons = $singletons;
        $this->codeStorage = $codeStorage;
    }
    
    public function getAdapterOf(Modules $modules) {
        $className = 'ModuleAdapter__'.$modules->hash();
        $fqcn = $this->codeStorage->load($className);
        if(empty($fqcn)) {
            $this->builder->build();
            $size = $this->singletons->getSize();
            
            $code = '

/**
 * Configured with
 *     '.$modules.'
 */
class '.$className.' {
    const SINGLETONS_SIZE = '.$size.';
}';
            
            $fqcn = $this->codeStorage->store($className, $code);
        }
        
        return $fqcn;
    }
}