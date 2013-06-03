<?php
namespace Spot\Reflect;

use Spot\Reflect\Reflection;
use Spot\Reflect\Impl\AnnotatedTrait;

class Type extends \ReflectionClass implements Annotated {
    use AnnotatedTrait;
    
    public function __construct($name, Reflection $reflection) {
        parent::__construct($name);
        
        $this->reflection = $reflection;
    }
    
    public function isSubTypeOf($super) {
        return 
            $this->reflection->getType($super)->isInterface()
            ? $this->implementsInterface($super)
            : $this->isSubclassOf($super);
    }
    
    public function getConstructor() {
        if(($ctor = parent::getConstructor())) {
            return $this->getMethod($ctor->name);
        }
    }
    
    public function getMethods($filter = null) {
        $methods = [];
        foreach($filter === null ? parent::getMethods() : parent::getMethods($filter) as $method) {
            $methods[] = $this->getMethod($method->name);
        }
        
        return $methods;
    }
    
    public function getMethod($name) {
        return $this->reflection->getMethod($this, $name);
    }
}