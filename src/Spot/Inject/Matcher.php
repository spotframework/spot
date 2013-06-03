<?php
namespace Spot\Inject;

use Spot\Reflect\Method;

interface Matcher {
    function matches(Method $method);
}