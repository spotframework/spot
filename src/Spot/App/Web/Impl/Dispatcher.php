<?php
namespace Spot\App\Web\Impl;

use Spot\App\Web\Impl\Dispatch\ClosureDispatcher;
use Spot\App\Web\Impl\Dispatch\FunctionDispatcher;
use Spot\App\Web\Impl\Dispatch\MethodDispatcher;
use Spot\App\Web\Request;
use Spot\App\Web\Response;
use Spot\Domain\Domain;
use Spot\Inject\Injector;
use Spot\Inject\Lazy;

class Dispatcher {
    private $method,
            $closure,
            $function;

    public function __construct(
            MethodDispatcher $method,
            ClosureDispatcher $closure,
            FunctionDispatcher $function) {
        $this->method = $method;
        $this->closure = $closure;
        $this->function = $function;
    }

    public function dispatch($action, Request $request, Response $response) {
        if(is_array($action)) {
            return $this->method->dispatch($action, $request, $response);
        }

        if($action instanceof \Closure) {
            return $this->closure->dispatch($action, $request, $response);
        }

        if(function_exists($action)) {
            return $this->function->dispatch($action, $request, $response);
        }
    }
}
