<?php
namespace Spot\Aspect\Intercept;

interface ConstructorInvocation extends Invocation {
    function getMethod();
}