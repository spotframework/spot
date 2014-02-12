<?php
namespace Spot\Inject\Aspect\Matcher;

use Spot\Inject\Aspect\MethodMatcher;
use Spot\Reflect\Method;

/** @Annotation */
class SubTypeOf implements MethodMatcher {
    public $value;

    function matches(Method $method) {
        return $method->getType()->isSubtypeOf($this->value);
    }
}
