<?php
namespace Spot\Inject\Impl\Aspect;

use Spot\Reflect\Type;
use Spot\Reflect\Reflection;
use Spot\Code\CodeStorage;

class AspectWeaver {
    private $pointCuts,
            $reflection,
            $factory;
    
    public function __construct(
            PointCuts $pointCuts,
            Reflection $reflection,
            ProxyFactory $factory) {
        $this->pointCuts = $pointCuts;
        $this->reflection = $reflection;
        $this->factory = $factory;
    }
    
    public function getPointCuts() {
        return $this->pointCuts;
    }
    
    public function getGenerator() {
        return $this->factory->getGenerator();
    }
    
    public function isInterceptedNamed($typeName) {
        return $this->isIntercepted($this->reflection->getType($typeName));
    }
    
    public function isIntercepted(Type $type) {
        return (bool)$this->pointCuts->getTypeAdvices($type);
    }
    
    public function getProxyNamed($typeName) {
        return $this->getProxy($this->reflection->getType($typeName));
    }
    
    public function getProxy(Type $type) {
        return $this->factory->getProxy($type);
    }

    public function isConstructorIntercepted(Type $type) {

    }

    public function isMethodIntercepted(Type $type) {

    }

    public function getMethodInterceptionProxy(Type $type) {

    }

    public function getConstructorInterceptionFactory(Type $type) {

    }
    
    static public function create(Reflection $reflection, CodeStorage $codeStorage) {
        $pointCuts = new PointCuts();
        $aspectGen = new ProxyGenerator();
        $aspectFactory = new ProxyFactory($codeStorage, $pointCuts, $aspectGen);
        $aspect = new AspectWeaver($pointCuts, $reflection, $aspectFactory);
        $aspectGen->setAspect($aspect);
        
        return $aspect;
    }
}