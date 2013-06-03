<?php
namespace Spot\Reflect;

use Spot\Reflect\Impl\AnnotatedTrait;

class Parameter extends \ReflectionParameter implements Annotated {
    use AnnotatedTrait;
    
    private $method;
    
    public function __construct(Method $method, $name, Reflection $reflection) {
        parent::__construct([$method->class, $method->name], $name);
        
        $this->method = $method;
        $this->reflection = $reflection;
    }
    
    public function getMethod() {
        return $this->method;
    }
    
    public function getClass() {
        if(($class = parent::getClass())) {
            return $this->reflection->getType($class->name);
        }
    }
}