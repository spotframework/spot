<?php
namespace Spot\Domain\Impl;

use Spot\Domain\DomainManager;
use Spot\Aspect\Intercept\MethodInvocation;
use Spot\Aspect\Intercept\MethodInterceptor;

class ValidationInterceptor implements MethodInterceptor {
    private $domain;
    
    public function __construct(DomainManager $domain) {
        $this->domain = $domain;
    }
    
    public function intercept(MethodInvocation $invocation) {
        $args = $invocation->getArguments();
        foreach($invocation->getMethod()->getParameters() as $i => $parameter) {
            $v = $parameter->getAnnotation("Spot\Domain\Validate");
            if($v) {
                $this->domain->validate($args[$i], $v->value);
            }
        }
        
        return $invocation->proceed();
    }    
}