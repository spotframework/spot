<?php
namespace Spot\Inject\Impl\Aspect;

use Spot\Aspect\Intercept\MethodInvocation;
use Spot\Aspect\Intercept\MethodInterceptor;

class DelegateInvocation implements MethodInvocation {
    public $invocation, $interceptor;
    
    public function __construct(
            MethodInvocation $invocation,
            MethodInterceptor $interceptor) {
        $this->invocation = $invocation;
        $this->interceptor = $interceptor;
    }
    
    public function getArguments() {
        return $this->invocation->getArguments();
    }

    public function getMethod() {
        return $this->invocation->getMethod();
    }

    public function proceed() {
        return $this->interceptor->intercept($this->invocation);
    }    
}