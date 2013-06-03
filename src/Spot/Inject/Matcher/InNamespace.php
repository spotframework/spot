<?php
namespace Spot\Inject\Matcher;

use Spot\Inject\Matcher;
use Spot\Reflect\Method;

/** @Annotation */
class InNamespace implements Matcher {
    public $value;
    
    public function matches(Method $method) {        
        return strpos($method->getType()->name, trim($this->value, '\\').'\\') === 0;
    }    
}