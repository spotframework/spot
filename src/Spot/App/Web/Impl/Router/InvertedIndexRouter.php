<?php
namespace Spot\App\Web\Impl\Router;

use Spot\App\Web\Impl\Router\InvertedIndex\AjaxIndex;
use Spot\App\Web\Impl\Router\InvertedIndex\MethodIndex;
use Spot\App\Web\Impl\Router\InvertedIndex\PrefixPathIndex;
use Spot\App\Web\Impl\Router\InvertedIndex\RegexPathIndex;
use Spot\App\Web\Impl\Router\InvertedIndex\StaticPathIndex;
use Spot\App\Web\Request;
use Spot\App\Web\Router;

abstract class InvertedIndexRouter implements Router {
    private $actions,
            $names,
            $method,
            $ajax,
            $staticPath,
            $prefixPath,
            $regexPath;

    public function __construct(
            array $actions,
            array $names,
            MethodIndex $method,
            AjaxIndex $ajax,
            StaticPathIndex $staticPath,
            PrefixPathIndex $prefixPath,
            RegexPathIndex $regexPath) {
        $this->actions = $actions;
        $this->names = $names;
        $this->method = $method;
        $this->ajax = $ajax;
        $this->staticPath = $staticPath;
        $this->prefixPath = $prefixPath;
        $this->regexPath = $regexPath;
    }

    function resolve(Request $request) {
        $actions = array_intersect_key(
            $this->actions,
            $this->staticPath->matches($request),
            $method = $this->method->matches($request),
            $ajax = $this->ajax->matches($request)
        );

        if($actions) {
            return explode("::", reset($actions));
        }

        $actions = array_intersect_key(
            $this->actions,
            $method,
            $ajax,
            $this->prefixPath->matches($request)
        );

        $actions = array_intersect_key(
            $actions,
            $this->regexPath->matches($request, $actions)
        );

        if($actions) {
            return explode("::", reset($actions));
        }
    }

    function generate($name, array $params = []) {
        if(isset($this->names[$name])) {
            if(key($params) === 0) {
                $params = array_combine(
                    $this->names[$name]["params"],
                    $params
                );
            }

            return str_replace(array_keys($params), $params, $this->names[$name]["uri"]);
        }
    }
}
