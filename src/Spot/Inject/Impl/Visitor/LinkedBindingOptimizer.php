<?php
namespace Spot\Inject\Impl\Visitor;

use Spot\Inject\Provides;
use Spot\Inject\Impl\Binding\SingletonBinding;
use Spot\Inject\Impl\Binding\ProviderMethodBinding;

/**
 * Optimize linked binding, which is a provider method binding
 * that return it's only dependency.
 */
class LinkedBindingOptimizer extends AbstractVisitor {    
    public function visitProviderMethod(ProviderMethodBinding $binding) {
        $method = $binding->getMethod();
        if($method->getNumberOfParameters() !== 1) {
            return;
        }
        
        $provides = $method->getAnnotation("Spot\Inject\Provides");
        if( $provides->value === Provides::CONSTANT 
            ||
            $provides->value === Provides::ELEMENT) {
            return;
        }
        
        $parameterType = $method->getParameters()[0]->getClass();        
        if(!$parameterType->isSubTypeOf($provides->value)) {
            return;
        }
        
        $filename = $method->getFileName();
        $lines = array_slice(file($filename), $method->getStartLine() - 1, $method->getEndLine() - $method->getStartLine() + 1);
        $source = implode($lines);
        
        if( preg_match('/static\s+function\s+[\w|\d|_]+\s*\(.*?(\$\w[\w|\d|_]+).*?\)\s*\{\s*return\s*(\$\w[\w|\d|_]+)\s*;/', $source, $matches)
            &&
            $matches[1] === $matches[2]) {
            $bindings = $this->getBindings();
                
            $key = $binding->getKey();
            $original = $bindings->get($key);
            $linked = $binding->getParameters()[0];

            if($original instanceof SingletonBinding) {
                $linked = new SingletonBinding($linked);
            }

            $bindings->put($key, $linked);
        }
    }
    
    public function visitSingleton(SingletonBinding $binding) {
        $binding->delegateAccept($this);
    }
}