<?php
namespace Spot\App\Web\Impl;

use Spot\Reflect\Method;
use Spot\Reflect\Parameter;
use Spot\App\Web\Impl\Binding\TypedBinding;
use Spot\App\Web\Impl\Binding\RequestBinding;
use Spot\App\Web\Impl\Binding\ResponseBinding;
use Spot\App\Web\Impl\Binding\OptionalBinding;
use Spot\App\Web\Impl\Binding\RequestVarBinding;
use Spot\App\Web\Impl\Binding\RequestTypedBinding;
use Spot\App\Web\Impl\Binding\RequestPropertyBinding;

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
            switch($class->name) {
                case "Spot\Http\Request":
                    return new RequestBinding();
                case "Spot\Http\Response":
                    return new ResponseBinding();
                case "Spot\Http\Request\Cookie":
                    return new RequestPropertyBinding("cookie");
                case "Spot\Http\Request\Server":
                    return new RequestPropertyBinding("server");
                case "Spot\Http\Request\Header":
                    return new RequestPropertyBinding("header");
                case "Spot\Http\Request\Body":
                    return new RequestPropertyBinding("body");
                case "Spot\Http\Request\Files":
                    return new RequestPropertyBinding("files");
            }
        }
        
        $name = $parameter->name;        
        $param = $parameter->getAnnotation("Spot\App\Web\Param");
        if($param && $param->value) {
            $name = $param->value;
        }
        
        $binding = $check = new RequestVarBinding($name);
        if($class) {
            if($param && empty($param->value)) {
                $binding = new RequestTypedBinding($class->name);
            } else {
                $binding = new TypedBinding($binding, $class->name);
            }
        }
        
        if($parameter->isDefaultValueAvailable()) {
            $binding = new OptionalBinding($check, $binding, $parameter->getDefaultValue());
        }
        
        return $binding;
    }
}
