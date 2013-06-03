<?php
namespace Spot\Inject\Impl\Aspect;

use Spot\Reflect\Type;
use Spot\Reflect\Method;
use Spot\Inject\Impl\Binding;

class PointCut {
    private $matcher,
            $binding;
    
    public function __construct(array $matcher, Binding $binding) {
        $this->matcher = $matcher;
        $this->binding = $binding;
    }
    
    public function getBinding() {
        return $this->binding;
    }
    
    public function matches(Method $method) {
        foreach($this->matcher as $matcher) {
            if($matcher->matches($method)) {
                return true;
            }
        }
        
        return false;
    }
}