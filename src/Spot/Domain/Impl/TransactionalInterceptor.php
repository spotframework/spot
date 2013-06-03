<?php
namespace Spot\Domain\Impl;

use Spot\Log\Log;
use Spot\Domain\DomainManager;
use Spot\Aspect\Intercept\MethodInvocation;
use Spot\Aspect\Intercept\MethodInterceptor;

class TransactionalInterceptor implements MethodInterceptor {
    private $domain,
            $level = 0;
    
    public function __construct(DomainManager $domain) {
        $this->domain = $domain;
    }
    
    /** @Log("{__CLASS__} intercepting method {invocation.method.type.name}::{invocation.method.name}") */
    public function intercept(MethodInvocation $invocation) {
        ++$this->level;
        
        try {            
            $result = $invocation->proceed();
            
            --$this->level or $this->domain->commit();
            
            return $result;
        } catch(\RuntimeException $e) {
            --$this->level or $this->domain->rollback();
            
            throw $e;
        }
    }    
}