<?php
namespace Spot\Aspect\Intercept;

interface ConstructorInterceptor {
    function intercept(ConstructorInvocation $invocation);
}