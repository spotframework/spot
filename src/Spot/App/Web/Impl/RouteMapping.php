<?php
namespace Spot\App\Web\Impl;

use Spot\App\Web\Route;

class RouteMapping {
    private $route,
            $method;

    public function __construct(Route $route, $method) {
        $this->route = $route;
        $this->method = $method;
    }

    public function getRoute() {
        return $this->route;
    }

    public function getMethod() {
        return $this->method;
    }
}