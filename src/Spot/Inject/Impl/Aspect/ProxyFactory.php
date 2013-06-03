<?php
namespace Spot\Inject\Impl\Aspect;

use Spot\Reflect\Type;
use Spot\Code\CodeStorage;

class ProxyFactory {
    private $storage,
            $pointCuts,
            $gen;
    
    public function __construct(
            CodeStorage $storage,
            PointCuts $pointCuts,
            ProxyGenerator $gen) {
        $this->storage = $storage;
        $this->pointCuts = $pointCuts;
        $this->gen = $gen;
    }
    
    public function getGenerator() {
        return $this->gen;
    }
    
    public function getProxy(Type $type) {
        $className = 'Aspect__'.md5($type->name);
        $fqcn = $this->storage->load($className);
        if(empty($fqcn)) {
            $advices = $this->pointCuts->getTypeAdvices($type);
            
            $code = $this->gen->generate($type, $className, $advices);

            $fqcn = $this->storage->store($className, $code);
        }
        
        return $fqcn;
    }
}