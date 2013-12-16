<?php
namespace Spot\Inject\Aspect\Matcher;

use Spot\Inject\Aspect\MethodMatcher;
use Spot\Reflect\Method;

class Only implements MethodMatcher {
    public $class;

    public $method;

    function matches(Method $method) {
        return
            $method->getType()->name == $this->class
            &&
            $method->name == $this->method;
    }
}
