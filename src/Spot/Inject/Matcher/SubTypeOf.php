<?php
namespace Spot\Inject\Matcher;

use Spot\Inject\Matcher;
use Spot\Reflect\Method;

/** @Annotation */
class SubTypeOf implements Matcher {
    public $value;
    
    public function matches(Method $method) {
        return $method->getType()->isSubTypeOf($this->value);
    }    
}