<?php
namespace Spot\Reflect;

use Spot\Reflect\Impl\Annotation\AnnotatedTrait;

class Parameter extends \ReflectionParameter implements Annotated {
    use AnnotatedTrait;

    private $method;

    public function __construct(Method $method, $parameter, Reflection $reflection) {
            parent::__construct([$method->class, $method->name], $parameter);

        $this->method = $method;
        $this->reflection = $reflection;
    }

    public function getMethod() {
        return $this->method;
    }

    public function getClass() {
        try {
            $class = parent::getClass();
        } catch(\ReflectionException $e) {
            throw new \ReflectionException(
                $e->getMessage().
                ", required by parameter \${$this->name} in ".
                $this->method->getType()->name."::{$this->method->name}()",
                $e->getCode()
            );
        }

        if($class) {
            return $this->reflection->get($class->name);
        }
    }
}
