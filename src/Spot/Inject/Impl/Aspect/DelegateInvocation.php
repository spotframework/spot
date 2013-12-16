<?php
namespace Spot\Inject\Impl\Aspect;

use Spot\Aspect\Intercept\MethodInterceptor;
use Spot\Aspect\Intercept\MethodInvocation;

class DelegateInvocation implements MethodInvocation {
    public $i, $d;

    public function __construct(MethodInterceptor $interceptor, MethodInvocation $delegate) {
        $this->i = $interceptor;
        $this->d = $delegate;
    }

    function getArguments() {
        return $this->d->getArguments();
    }

    function proceed() {
        return $this->i->intercept($this->d);
    }

    function getMethod() {
        return $this->d->getMethod();
    }
}
