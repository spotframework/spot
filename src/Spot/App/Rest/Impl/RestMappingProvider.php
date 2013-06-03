<?php
namespace Spot\App\Rest\Impl;

use Spot\App\Rest\Path;
use Spot\App\Web\Impl\ControllerMappingProvider;
use Spot\App\Web\Impl\MappingProvider;
use Spot\App\Web\Impl\RouteMapping;
use Spot\App\Web\Route;
use Spot\Reflect\Method;

class RestMappingProvider extends ControllerMappingProvider {
    private $scanner;

    public function __construct(ResourceScanner $scanner) {
        $this->scanner = $scanner;
    }

    public function getMappings() {
        $mappings = [];
        foreach($this->scanner->scan() as $resource) {
            $resourcePath = $resource->getAnnotation("Spot\App\Rest\Resource")->value;
            foreach($resource->getMethods(Method::IS_PUBLIC) as $method) {
                if($method->isAnnotatedWith("Spot\App\Rest\Impl\RequestMethod")) {
                    $path = $method->getAnnotation("Spot\App\Rest\Path") ?: new Path();
                    $route = new Route();

                    $route->value = $resourcePath.$path->value;
                    $route->method = [(string)$method->getAnnotation("Spot\App\Rest\Impl\RequestMethod")];
                    $route->params = [];

                    $methodName = $method->getType()->name."::".$method->name;
                    foreach(["", ".json", ".xml", ".csv"] as $format) {
                        $formattedRoute = clone $route;
                        $formattedRoute->value = $formattedRoute->value.$format;

                        $mappings[] = new RouteMapping($formattedRoute, $methodName);
                    }
                }
            }
        }

        return $mappings;
    }
}