<?php
namespace Spot\Domain\Impl;

use Spot\Log\Log;
use Spot\Domain\DomainManager;
use Spot\Aspect\Intercept\MethodInvocation;
use Spot\Aspect\Intercept\MethodInterceptor;

class TransactionalInterceptor implements MethodInterceptor {
    private $domain,
            $lock = 0;
    
    public function __construct(DomainManager $domain) {
        $this->domain = $domain;
    }
    
    public function intercept(MethodInvocation $invocation) {
        try {
            $this->lock or $this->domain->begin();
            
            ++$this->lock;
            
            $result = $invocation->proceed();
            
            --$this->lock or $this->domain->commit();
            
            return $result;
        } catch(\RuntimeException $e) {
            --$this->lock or $this->domain->rollback();
            
            throw $e;
        }
    }    
}