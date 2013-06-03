<?php
namespace Spot\Reflect;

use Spot\Reflect\Impl\AnnotatedTrait;

class Method extends \ReflectionMethod implements Annotated {
    use AnnotatedTrait;
    
    private $type;
    
    public function __construct(
            Type $type,
            $name,
            Reflection $reflection) {
        parent::__construct($type->name, $name);
        
        $this->type = $type;
        $this->reflection = $reflection;
    }
    
    public function getType() {
        return $this->type;
    }
    
    public function getParameters() {
        $parameters = [];
        foreach(parent::getParameters() as $parameter) {
            $parameters[] = $this->reflection->getParameter($this, $parameter->name);
        }
        
        return $parameters;
    }
    
    public function hasParameterAnnotatedWith($annotation) {
        foreach($this->getParameters() as $parameter) {
            if($parameter->isAnnotatedWith($annotation)) {
                return true;
            }
        }
        
        return false;
    }
}