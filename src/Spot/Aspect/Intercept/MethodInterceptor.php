<?php
namespace Spot\Aspect\Intercept;

interface MethodInterceptor extends Interceptor {
    function intercept(MethodInvocation $invocation);
}