<?php
namespace Spot\Aspect\Intercept;

interface MethodInvocation extends Invocation {
    function getMethod();
}