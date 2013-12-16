<?php
namespace Spot\Domain\Impl;

use Spot\Aspect\Intercept\MethodInterceptor;
use Spot\Aspect\Intercept\MethodInvocation;
use Spot\Domain\Domain;

class TransactionalInterceptor implements MethodInterceptor {
    private $domain,
            $level = 0;

    public function __construct(Domain $domain) {
        $this->domain = $domain;
    }

    function intercept(MethodInvocation $invocation) {
        $this->level++ && $this->domain->beginTransaction();

        try {
            $result = $invocation->proceed();

            --$this->level || $this->domain->commit();

            return $result;
        } catch(\Exception $e) {
            --$this->level || $this->domain->rollback();

            throw $e;
        }
    }
}
