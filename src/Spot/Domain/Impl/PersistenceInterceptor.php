<?php
namespace Spot\Domain\Impl;

use Spot\Domain\DomainManager;
use Spot\Aspect\Intercept\MethodInvocation;
use Spot\Aspect\Intercept\MethodInterceptor;

class PersistenceInterceptor implements MethodInterceptor {
    private $domain;
    
    public function __construct(DomainManager $domain) {
        $this->domain = $domain;
    }
    
    public function intercept(MethodInvocation $invocation) {
        $args = $invocation->getArguments();
        foreach($invocation->getMethod()->getParameters() as $i => $parameter) {
            if($parameter->isAnnotatedWith('Spot\Domain\Persist')) {
                $this->domain->persist($args[$i]);
            }
            
            if($parameter->isAnnotatedWith('Spot\Domain\Remove')) {
                $this->domain->remove($args[$i]);
            }
        }
    }    
}