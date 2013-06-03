<?php
namespace Spot\Inject\Impl;

use Spot\Inject\Key;
use Spot\Inject\Named;
use Spot\Inject\TypeKey;
use Spot\Inject\Injector;
use Spot\Reflect\Reflection;
use Spot\Code\CodeStorage;
use Spot\Inject\Impl\Aspect\AspectWeaver;
use Spot\Inject\Impl\Binder\BuiltInBinder;
use Spot\Inject\Impl\Lazy\LazyFactory;
use Spot\Inject\Impl\Lazy\LazyProxyGenerator;
use Spot\Inject\Impl\Visitor\GraphVizVisitor;
use Spot\Inject\Impl\Binding\ConstantBinding;

class InjectorImpl implements Injector {    
    private $modules,
            $bindings,
            $singletons,
            $factory,
            $lazy,
            $aspect;
    
    public function __construct(
            Modules $modules,
            Bindings $bindings,
            SingletonPool $singletons,
            FactoryFactory $factory,
            LazyFactory $lazy,
            AspectWeaver $aspect) {
        $this->modules = $modules;
        $this->bindings = $bindings;
        $this->singletons = $singletons;
        $this->factory = $factory;
        $this->lazy = $lazy;
        $this->aspect = $aspect;
    }
    
    public function get(Key $key) {
        $factory = $this->factory->getFactory($key);
        
        return $factory::get($this->singletons, $this, $this->modules);
    }

    public function getInstance($typeName) {        
        return $this->get(Key::ofType($typeName));
    }

    public function getLazy(TypeKey $key) {
        $fqcn = $this->lazy->get($key);
                
        return new $fqcn($this, $key);
    }
    
    public function getWovenProxy(TypeKey $key, $delegate) {
        $proxyClass = $this->aspect->getProxyNamed($key->getTypeName());
        
        return new $proxyClass(
            $this->getInstance("Spot\Reflect\Reflection"),
            $this,
            $delegate
        );
    }

    public function getSingletonPool() {
        return $this->singletons;
    }
    
    public function fork(array $modules) {
        $modules = new Modules(array_merge(
            $this->getModules(),
            $modules
        ));
        
        $singletons = $this->singletons->link();
        $bindings = $this->bindings->link();
        
        $aspect = AspectWeaver::create(
            $this->getInstance("Spot\Reflect\Reflection"),
            $this->getInstance("Spot\Code\CodeStorage")
        );
        
        $factory = $this->factory->link($modules, $bindings, $singletons, $aspect);
        
        return new self($modules, $bindings, $singletons, $factory, $this->lazy, $aspect);
    }
    
    public function getModules() {
        return iterator_to_array($this->modules);
    }
    
    static public function create(
            array $modules, 
            array $constants,
            Reflection $reflection, 
            CodeStorage $codeStorage) {
        $modules = new Modules($modules);
        $bindings = new Bindings();
        $singletons = new SingletonPool();
        
        $builtIn = new BuiltInBinder($bindings, $singletons);
        $builtIn->bind(Key::ofType("Spot\Reflect\Reflection"), $reflection);
        $builtIn->bind(Key::ofType("Spot\Code\CodeStorage"), $codeStorage);
        foreach($constants as $name => $value) {
            $key = Key::ofConstant(Named::name($name));
            $bindings->put($key, new ConstantBinding($key, $value));
        }
        
        $aspect = AspectWeaver::create($reflection, $codeStorage);
        $factory = FactoryFactory::create($modules, $bindings, $reflection, $codeStorage, $singletons, $aspect);
        
        $lazyGen = new LazyProxyGenerator();
        $lazy = new LazyFactory($codeStorage, $reflection, $lazyGen);
        
        return new self(
            $modules,
            $bindings,
            $singletons,
            $factory,
            $lazy,
            $aspect
        );
    }
}