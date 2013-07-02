<?php
namespace Spot\App\Web\Impl;

use Spot\Reflect\Method;
use Spot\App\Web\Route;
use Spot\Http\Request;

class ControllerMappingProvider implements MappingProvider {
    private $scanner;

    public function __construct(ControllerScanner $scanner) {
        $this->scanner = $scanner;
    }

    public function getMappings() {
        $mappings = [];
        foreach($this->scanner->scan() as $controller) {
            $cRoute = $controller->getAnnotation("Spot\App\Web\Route") ?: new Route();
            foreach($controller->getMethods(Method::IS_PUBLIC) as $method) {
                if(!$method->isAnnotatedWith("Spot\App\Web\Route")) {
                    continue;
                }

                $mRoute = $method->getAnnotation("Spot\App\Web\Route");

                $route = new Route();
                $route->value = $cRoute->value.$mRoute->value;
                $route->method = $mRoute->method ?: $cRoute->method ?: [Request::GET, Request::POST];
                $route->ajax = (bool)($mRoute->ajax === null ? $cRoute->ajax : $mRoute->ajax);
                $route->params = $mRoute->params ?: $cRoute->params ?: [];

                if(!isset($route->value[0]) || $route->value[0] !== "/") {
                    throw new \LogicException("Route url must be started with \"/\", in {$controller->name}::{$method->name}()");
                }
                
                $mappings[] = new RouteMapping($route, $controller->name.'::'.$method->name);
            }
        }

        return $mappings;
    }
}