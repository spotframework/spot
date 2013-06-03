<?php
namespace Spot\App\Web\Impl;

use Spot\Http\Request;
use Spot\Http\Response;

interface Action {
    function invoke(Request $request, Response $response);
}