<?php
namespace Spot\App\REST\Impl;

use Spot\App\REST\Path;
use Spot\App\Web\Impl\Router\InvertedIndex\ActionMapping;
use Spot\App\Web\Impl\Router\InvertedIndex\ControllerScanner;
use Spot\App\Web\Route;
use Spot\Reflect\Match;
use Spot\Reflect\Method;
use Spot\Reflect\Reflection;
use Spot\Inject\Named;

class ResourceScanner extends ControllerScanner {
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
        $matcher = Match::annotatedWith("Spot\\App\\REST\\Resource");
        foreach($this->namespaces as $ns) {
            foreach($this->reflection->find($ns, $matcher) as $type) {
                $resource = $type->getAnnotation("Spot\\App\\REST\\Resource");
                foreach($type->getMethods(Method::IS_PUBLIC) as $method) {
                    if(!$method->isAnnotatedWith("Spot\\App\\REST\\Impl\\RequestMethod")) {
                        continue;
                    }

                    $path = $method->getAnnotation("Spot\\App\\REST\\Path") ?: new Path();

                    $route = new Route();

                    $route->value = $resource->value.$path->value;
                    $route->method = (string)$method->getAnnotation("Spot\\App\\REST\\Impl\\RequestMethod");

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
