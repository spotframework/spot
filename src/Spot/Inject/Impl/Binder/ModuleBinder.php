<?php
namespace Spot\Inject\Impl\Binder;

use Spot\Inject\Key;
use Spot\Inject\Lazy;
use Spot\Inject\TypeKey;
use Spot\Reflect\Type;
use Spot\Reflect\Method;
use Spot\Reflect\Parameter;
use Spot\Reflect\Reflection;
use Spot\Inject\ElementKey;
use Spot\Inject\Impl\Bindings;
use Spot\Inject\Impl\Aspect\PointCut;
use Spot\Inject\Impl\Aspect\PointCuts;
use Spot\Inject\Impl\Binding\LazyBinding;
use Spot\Inject\Impl\Binding\OptionalBinding;
use Spot\Inject\Impl\Binding\SingletonBinding;
use Spot\Inject\Impl\Binding\CollectionBinding;
use Spot\Inject\Impl\Binding\UnresolvedBinding;
use Spot\Inject\Impl\Binding\ProviderMethodBinding;

class ModuleBinder {
    private $reflection,
            $bindings,
            $pointCuts;
    
    public function __construct(
            Reflection $reflection,
            Bindings $bindings,
            PointCuts $pointCuts) {
        $this->reflection = $reflection;
        $this->bindings = $bindings;
        $this->pointCuts = $pointCuts;
    }
    
    public function bindNamed($moduleName) {
        return $this->bind($this->reflection->getType($moduleName));
    }
    
    public function bind(Type $module) {
        foreach($module->getMethods() as $method) {
            if($method->isAnnotatedWith("Spot\Inject\Provides")) {
                $this->bindProvider($method);
                
                if($method->isAnnotatedWith("Spot\Inject\Intercept")) {
                    $this->bindInterceptor($method);
                }
            }
        }
    }
    
    public function bindProvider(Method $method) {
        $key = Key::ofProvider($method);
        
        $parameters = [];
        foreach($method->getParameters() as $parameter) {
            $parameters[] = $this->bindParameter($parameter);
        }
        
        $binding = new ProviderMethodBinding($key, $method, $parameters);
        if( ($configured = $this->bindings->get($key)) 
            && 
            (
                $configured instanceof ProviderMethodBinding
                ||
                $configured instanceof SingletonBinding
            )) {
            
            if($configured instanceof SingletonBinding) {
                $configured = $configured->getDelegate();
            }
            
            throw new \LogicException(
                "Binding for {$key}  in ".
                $method->getFileName().":".$method->getStartLine().
                " is already configured in ".
                $configured->getMethod()->getFileName().":".$configured->getMethod()->getStartLine());
        }
        
        if($method->isAnnotatedWith('Spot\Inject\Singleton')) {
            $binding = new SingletonBinding($binding);
        }
        
        if($key instanceof ElementKey) {
            $collectionBinding = $this->bindings->get($key);
            if(empty($collectionBinding)) {
                $collectionBinding = new CollectionBinding($key);
                
                $this->bindings->put($key, $collectionBinding);
            }
            
            $collectionBinding->add($binding);
        } else {
            $this->bindings->put($key, $binding);
        }
        
        return $binding;
    }
    
    public function bindInterceptor(Method $method) {
        $key = Key::ofProvider($method);
        if( !$key instanceof TypeKey
            ||
            (
                $key instanceof TypeKey
                &&
                !$this->reflection->getType($key->getTypeName())->isSubTypeOf('Spot\Aspect\Intercept\MethodInterceptor')
            )) {
            
            throw new \LogicException(
                'Interceptor method '.
                $method->getType()->name.'::'.$method->name.
                ' must provides implementation of '.
                'Spot\Aspect\Intercept\MethodInterceptor in '.
                $method->getFileName().':'.$method->getStartLine()
            );
        }
        
        $binding = $this->bindings->get($key);
        foreach($method->getAnnotations('Spot\Inject\Intercept') as $intercept) {
            $pointCut = new PointCut($intercept->getMatchers(), $binding);

            $this->pointCuts->put($pointCut);
        }
    }
    
    public function bindParameter(Parameter $parameter) {
        $key = Key::ofParameter($parameter);
        
            $binding = new UnresolvedBinding($key, $parameter);
            
            if($parameter->isDefaultValueAvailable()) {
                $binding = new OptionalBinding($binding, $parameter->getDefaultValue());
            }
        
        
        if($key instanceof TypeKey && $parameter->isAnnotatedWith('Spot\Inject\Lazy')) {
            $binding = new LazyBinding($key, $binding);
        }

        return $binding;
    }
}