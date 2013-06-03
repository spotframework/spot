<?php
namespace Spot\Inject\Impl\Lazy;

use Spot\Inject\TypeKey;
use Spot\Code\CodeStorage;
use Spot\Reflect\Reflection;

class LazyFactory {
    private $storage,
            $reflection,
            $lazyGen;
    
    public function __construct(
            CodeStorage $storage, 
            Reflection $reflection,
            LazyProxyGenerator $lazyGen) {
        $this->storage = $storage;
        $this->reflection = $reflection;
        $this->lazyGen = $lazyGen;
    }
    
    public function get(TypeKey $key) {
        $className = 'Lazy__'.$key->hash();
        $fqcn = $this->storage->load($className);
        if(empty($fqcn)) {
            $code = $this->lazyGen->generate($this->reflection->getType($key->getTypeName()), $className);
            
            $this->storage->store($className, $code);
            
            $fqcn = $this->storage->load($className);
        }
        
        return $fqcn;
    }
}