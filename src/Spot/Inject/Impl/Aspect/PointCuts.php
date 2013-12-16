<?php
namespace Spot\Inject\Impl\Aspect;

use Spot\Reflect\Method;

class PointCuts {
    private $pointCuts = [];

    public function put(PointCut $pointCut) {
        $this->pointCuts[] = $pointCut;
    }

    public function matches(Method $method) {
        if($method->isConstructor() || $method->isDestructor() || $method->isStatic()) {
            return false;
        }

        foreach($this->pointCuts as $pointCut) {
            if($pointCut->matches($method)) {
                return true;
            }
        }

        return false;
    }

    public function getAdvices(Method $method) {
        $advices = [];
        foreach($this->pointCuts as $pointCut) {
            if($pointCut->matches($method)) {
                $advices[] = $pointCut->getAdvice();
            }
        }

        return $advices;
    }
}
