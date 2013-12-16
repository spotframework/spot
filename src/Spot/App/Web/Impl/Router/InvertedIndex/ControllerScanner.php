<?php
namespace Spot\App\Web\Impl\Router\InvertedIndex;

use Spot\App\Web\Request;
use Spot\App\Web\Route;
use Spot\Reflect\Match;
use Spot\Reflect\Method;
use Spot\Reflect\Reflection;
use Spot\Inject\Named;

class ControllerScanner {
    private $reflection,
            $namespaces;

    public function __construct(
            Reflection $reflection,
            /** @Named("app.module.namespaces") */array $namespaces = []) {
        $this->reflection = $reflection;
        $this->namespaces = $namespaces;
    }

    public function scan() {
        $mappings = [];
        $matcher = Match::annotatedWith("Spot\\App\\Web\\Controller");
        foreach($this->namespaces as $ns) {
            foreach($this->reflection->find($ns, $matcher) as $type) {
                $typeRoute = $type->getAnnotation("Spot\\App\\Web\\Route") ?: new Route();
                foreach($type->getMethods(Method::IS_PUBLIC) as $method) {
                    if(!$method->isAnnotatedWith("Spot\\App\\Web\\Route")) {
                        continue;
                    }

                    $route = $method->getAnnotation("Spot\\App\\Web\\Route");
                    $route->value = $typeRoute->value.$route->value;
                    $route->ajax = $route->ajax === null ? $typeRoute->ajax : $route->ajax;
                    $route->method = $route->method ?: $typeRoute->method ?: [Request::GET, Request::POST];

                    if(!isset($route->value[0]) || $route->value[0] != "/") {
                        throw new \LogicException("Controller uri must start with \"/\"");
                    }

                    $mappings[] = new ActionMapping(
                        $route,
                        $method->getType()->name."::".$method->name
                    );
                }
            }
        }

        return $mappings;
    }
}
