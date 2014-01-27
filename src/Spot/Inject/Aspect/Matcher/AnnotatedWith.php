<?php
namespace Spot\Inject\Aspect\Matcher;

use Spot\Inject\Aspect\MethodMatcher;
use Spot\Reflect\Method;

/** @Annotation */
class AnnotatedWith implements MethodMatcher {
    public $value;

    function matches(Method $method) {
        if( $method->isAnnotatedWith($this->value)
            ||
            $method->getType()->isAnnotatedWith($this->value)) {
            return true;
        }

        foreach($method->getParameters() as $parameter) {
            if($parameter->isAnnotatedWith($this->value)) {
                return true;
            }
        }

        return false;
    }
}
