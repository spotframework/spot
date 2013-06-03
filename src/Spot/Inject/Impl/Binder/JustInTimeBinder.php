<?php
namespace Spot\Inject\Impl\Binder;

use Spot\Inject\Key;
use Spot\Inject\TypeKey;
use Spot\Reflect\Type;
use Spot\Reflect\Parameter;
use Spot\Reflect\Reflection;
use Spot\Inject\Impl\Bindings;
use Spot\Inject\Impl\Binding\LazyBinding;
use Spot\Inject\Impl\Binding\InlineBinding;
use Spot\Inject\Impl\Binding\ConstantBinding;

class JustInTimeBinder {
    private $reflection,
            $bindings;
    
    public function __construct(
            Reflection $reflection,
            Bindings $bindings) {
        $this->reflection = $reflection;
        $this->bindings = $bindings;
    }
    
    public function bindNamed($typeName) {
        return $this->bind($this->reflection->getType($typeName));
    }
    
    public function bind(Type $class) {
        if(!$class->isInstantiable()) return;
        
        $key = Key::ofType($class->name);
        
        $parameters = [];
        foreach(($ctor = $class->getConstructor()) 
                ? $ctor->getParameters() 
                : [] as $parameter) {
            $parameters[] = $this->bindParameter($parameter);
        }
        
        $binding = new InlineBinding($key, $class->name, $parameters);
        
        $this->bindings->put($key, $binding);
    }
    
    public function bindParameter(Parameter $parameter) {
        $key = Key::ofParameter($parameter);
        $binding = $this->bindings->get($key);
        if(empty($binding)) {
            $class = $parameter->getClass();
            if($class && $class->isInstantiable() && !$parameter->isOptional()) {
                $this->bind($parameter->getClass());
            }
            
            $binding = $this->bindings->get($key);
            if(empty($binding) && $parameter->isOptional()) {
                $binding = new ConstantBinding($key, $parameter->getDefaultValue());
            }
            
            if(empty($binding)) {
                throw new \LogicException(
                    'Missing binding for '.$key.
                    ', required by parameter $'.$parameter->name.' in '.$parameter->getDeclaringClass()->name.'::'.$parameter->getDeclaringFunction()->name.'()');
            }
        }
        
        if($key instanceof TypeKey && $parameter->isAnnotatedWith('Spot\Inject\Lazy')) {
            $binding = new LazyBinding($key, $binding);
        }
        
        return $binding;
    }
}