<?php
namespace Spot\App\Web\Impl;

use Spot\Http\Request;
use Spot\Reflect\Reflection;

class Router {
    private $reflection,
            $strategy,
            $factory;
    
    public function __construct(
            Reflection $reflection,
            RoutingStrategy $strategy,
            ActionFactory $factory) {
        $this->reflection = $reflection;
        $this->strategy = $strategy;
        $this->factory = $factory;
    }
    
    public function resolve(Request $request) {
        $method = $this->strategy->resolve($request);
        $method = $this->reflection->getMethod(
            $this->reflection->getType($method[0]),
            $method[1]
        );
        
        return $this->factory->create($method);
    }
}