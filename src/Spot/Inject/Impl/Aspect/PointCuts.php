<?php
namespace Spot\Inject\Impl\Aspect;

use Spot\Reflect\Type;
use Spot\Reflect\Method;

class PointCuts {
    private $pointCuts = [];
    
    public function put(PointCut $pointCut) {
        $this->pointCuts[] = $pointCut;
    }
    
    public function getTypeAdvices(Type $type) {        
        $map = [];
        foreach($type->getMethods(Method::IS_PUBLIC) as $method) {
            if(($advices = $this->getMethodAdvices($method))) {
                $map[$method->name] = $advices;
            }
        }
        return $map;
    }
    
    public function getMethodAdvices(Method $method) {
        $advices = [];        
        foreach($this->pointCuts as $pointCut) {
            if($pointCut->matches($method)) {
                $advices[] = $pointCut->getBinding();
            }
        }
        
        return $advices;
    }
}