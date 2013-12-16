<?php
namespace Spot\Reflect\Impl\Matcher;

use Spot\Reflect\Matcher;
use Spot\Reflect\Type;

class AnnotatedWith extends Matcher {
    public $a;

    public function __construct($annotation) {
        $this->a = $annotation;
    }

    public function matches(Type $type) {
        return $type->isAnnotatedWith($this->a);
    }
}
