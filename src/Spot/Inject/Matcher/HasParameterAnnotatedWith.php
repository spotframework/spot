<?php
namespace Spot\Inject\Matcher;

use Spot\Reflect\Method;
use Spot\Inject\Matcher;

/** @Annotation */
class HasParameterAnnotatedWith implements Matcher {
    public $value;
    
    public function matches(Method $method) {
        return $method->hasParameterAnnotatedWith($this->value);
    }    
}