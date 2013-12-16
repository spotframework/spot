<?php
namespace Spot\Inject\Aspect;

use Spot\Reflect\Method;

interface MethodMatcher {
    function matches(Method $method);
}
