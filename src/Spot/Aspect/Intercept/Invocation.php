<?php
namespace Spot\Aspect\Intercept;

use Spot\Aspect\JoinPoint;

interface Invocation extends JoinPoint {
    function getArguments();
}