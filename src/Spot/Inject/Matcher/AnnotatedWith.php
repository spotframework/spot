<?php
namespace Spot\Inject\Matcher;

use Spot\Reflect\Method;
use Spot\Inject\Matcher;

/** @Annotation */
class AnnotatedWith implements Matcher {
    public $value;
    
    public function matches(Method $method) {
        return $method->isAnnotatedWith($this->value);
    }
}