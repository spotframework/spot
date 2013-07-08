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
    
    public function intercept(MethodInvocation $invocation) {
        
        
        try {
            $this->level or $this->domain->begin();
            
            ++$this->level;
            
            $result = $invocation->proceed();
            
            --$this->level or $this->domain->commit();
            
            return $result;
        } catch(\RuntimeException $e) {
            --$this->level or $this->domain->rollback();
            
            throw $e;
        }
    }    
}