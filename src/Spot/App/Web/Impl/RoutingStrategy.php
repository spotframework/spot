<?php
namespace Spot\App\Web\Impl;

use Spot\Http\Request;

interface RoutingStrategy {
    function resolve(Request $request);
}