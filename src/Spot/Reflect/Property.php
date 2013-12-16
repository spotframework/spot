<?php
namespace Spot\Reflect;

use Spot\Reflect\Impl\Annotation\AnnotatedTrait;

class Property extends \ReflectionProperty implements Annotated {
    use AnnotatedTrait;

    private $type;

    public function __construct(Type $type, $property, Reflection $reflection) {
        parent::__construct($type->name, $property);

        $this->type = $type;
        $this->reflection = $reflection;
    }

    public function getType() {
        return $this->type;
    }
}
