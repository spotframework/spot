<?php
namespace Spot\Reflect;

use Spot\Reflect\Impl\Annotation\AnnotatedTrait;

class Type extends \ReflectionClass implements Annotated {
    use AnnotatedTrait;

    private $methods = [],
            $properties = [];

    public function __construct($type, Reflection $reflection) {
        parent::__construct($type);

        $this->reflection = $reflection;
    }

    public function getConstructor() {
        if(($ctor = parent::getConstructor())) {
            return $this->getMethod($ctor->name);
        }
    }

    public function getMethods($filter = null) {
        $methods = [];
        foreach($filter ? parent::getMethods($filter) : parent::getMethods() as $method) {
            $methods[] = $this->getMethod($method->name);
        }

        return $methods;
    }

    public function getMethod($name) {
        return isset($this->methods[$name])
            ? $this->methods[$name]
            : $this->methods[$name] = new Method($this, $name, $this->reflection);
    }

    public function getProperties($filter = null) {
        $properties = [];
        foreach($filter ? parent::getProperties($filter) : parent::getProperties() as $property) {
            $properties[] = $this->getProperty($property->name);
        }

        return $properties;
    }

    public function getProperty($name) {
        return isset($this->properties[$name])
            ? $this->properties[$name]
            : $this->properties[$name] = new Property($this, $name, $this->reflection);
    }

    public function isSubtypeOf($super) {
        return interface_exists($super)
            ? $this->implementsInterface($super)
            : $this->isSubclassOf($super);
    }
}
