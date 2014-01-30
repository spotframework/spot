<?php
namespace Spot\Domain\Impl;

use Spot\Aspect\Intercept\MethodInterceptor;
use Spot\Aspect\Intercept\MethodInvocation;
use Spot\Domain\Domain;

class PersistenceInterceptor implements MethodInterceptor {
    private $domain;

    public function __construct(Domain $domain) {
        $this->domain = $domain;
    }

    function intercept(MethodInvocation $invocation) {
        $args = $invocation->getArguments();

        foreach($invocation->getMethod()->getParameters() as $i => $parameter) {
            if($parameter->isAnnotatedWith("Spot\\Domain\\Persist")) {
                $this->domain->persist($args[$i]);
            } else if($parameter->isAnnotatedWith("Spot\\Domain\\Remove")) {
                $this->domain->remove($args[$i]);
            }
        }

        return $invocation->proceed();
    }
}
