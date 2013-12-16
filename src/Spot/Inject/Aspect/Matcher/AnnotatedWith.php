<?php
namespace Spot\Inject\Aspect\Matcher;

use Spot\Inject\Aspect\MethodMatcher;
use Spot\Reflect\Method;

/** @Annotation */
class AnnotatedWith implements MethodMatcher {
    public $value;

    function matches(Method $method) {
        return
            $method->isAnnotatedWith($this->value)
            ||
            $method->getType()->isAnnotatedWith($this->value);
    }
}
