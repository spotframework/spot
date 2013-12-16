<?php
namespace Spot\App\Web\Impl\Dispatch;

use Spot\App\Web\Request;
use Spot\App\Web\Response;

class ClosureDispatcher {
    public function dispatch(\Closure $action, Request $request, Response $response) {
        return $action($request, $response);
    }
}
