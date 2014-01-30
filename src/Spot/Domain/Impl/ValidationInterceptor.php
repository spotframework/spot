<?php
namespace Spot\Domain\Impl;

use Spot\Aspect\Intercept\MethodInterceptor;
use Spot\Aspect\Intercept\MethodInvocation;
use Spot\Domain\Validator;

class ValidationInterceptor implements MethodInterceptor {
    private $validator;

    public function __construct(Validator $validator) {
        $this->validator = $validator;
    }

    function intercept(MethodInvocation $invocation) {
        $args = $invocation->getArguments();
        foreach($invocation->getMethod()->getParameters() as $i => $parameter) {
            if($parameter->isAnnotatedWith("Spot\\Domain\\Validate")) {
                $validate = $parameter->getAnnotation("Spot\\Domain\\Validate");

                $this->validator->validate($args[$i], $validate->groups);
            }
        }

        return $invocation->proceed();
    }
}
