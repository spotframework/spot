<?php
namespace Spot\Reflect;

use Spot\Reflect\Impl\Annotation\AnnotatedTrait;

class Method extends \ReflectionMethod implements Annotated {
    use AnnotatedTrait;

    private $type,
            $parameters = [];

    public function __construct(Type $type, $method, Reflection $reflection) {
        parent::__construct($type->name, $method);

        $this->type = $type;
        $this->reflection = $reflection;
    }

    public function getType() {
        return $this->type;
    }

    public function getParameters() {
        if(empty($this->parameters)) {
            foreach(parent::getParameters() as $i => $parameter) {
                $this->parameters[$i] = new Parameter($this, $parameter->name, $this->reflection);
            }
        }

        return $this->parameters;
    }

    public function hasParametersAnnotatedWith($annotation) {
        foreach($this->getParameters() as $parameter) {
            if($parameter->isAnnotatedWith($annotation)) {
                return true;
            }
        }

        return false;
    }
}
