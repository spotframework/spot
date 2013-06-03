<?php
namespace Spot\App\Cli\Impl;

use Spot\Inject\Injector;
use Spot\App\Cli\Args;

class Router {
    private $injector,
            $strategy,
            $factory;

    public function __construct(
            Injector $injector,
            RoutingStrategy $strategy,
            ActionFactory $factory) {
        $this->injector = $injector;
        $this->strategy = $strategy;
        $this->factory = $factory;
    }

    public function resolve(Args $args) {
        $method = $this->strategy->resolve($args);
        $action = $this->factory->create($method);

        return new $action($this->injector);
    }
}