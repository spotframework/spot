<?php
namespace Spot\App\Web\Impl;

use Spot\Reflect\Method;
use Spot\Reflect\Parameter;
use Spot\App\Web\Impl\Binding\TypedBinding;
use Spot\App\Web\Impl\Binding\RequestBinding;
use Spot\App\Web\Impl\Binding\ResponseBinding;
use Spot\App\Web\Impl\Binding\RequestVarBinding;

class BindingExtractor {
    public function extract(Method $method) {
        $bindings = [];
        foreach($method->getParameters() as $parameter) {
            $bindings[] = $this->extractParameter($parameter);
        }
        
        return $bindings;
    }
    
    public function extractParameter(Parameter $parameter) {
        if(($class = $parameter->getClass())) {
            if($class->name === "Spot\Http\Request") {
                return new RequestBinding();
            } else if($class->name === "Spot\Http\Response") {
                return new ResponseBinding();
            }
        }
        
        $name = $parameter->name;        
        if(($bind = $parameter->getAnnotation("Spot\Domain\Bind\Bind")) && $bind->value) {
            $name = $bind->value;
        }
        
        $binding = new RequestVarBinding($name);
        if($class) {            
            $binding = new TypedBinding($binding, $class->name);
        }
        
        return $binding;
    }
}