<?php
namespace Spot\Inject\Impl\Aspect;

use Spot\Inject\Binding;
use Spot\Reflect\Method;

class PointCut {
    private $matchers,
            $advice;

    public function __construct(array $matchers, Binding $advice) {
        $this->matchers = $matchers;
        $this->advice = $advice;
    }

    public function matches(Method $method) {
        foreach($this->matchers as $matcher) {
            if(!$matcher->matches($method)) {
                return false;
            }
        }

        return true;
    }

    public function getAdvice() {
        return $this->advice;
    }
}
