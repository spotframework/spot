<?php
namespace Spot\App\Web\Impl\Router;

use Spot\App\Web\Impl\Router\InvertedIndex\AjaxIndex;
use Spot\App\Web\Impl\Router\InvertedIndex\MethodIndex;
use Spot\App\Web\Impl\Router\InvertedIndex\PrefixPathIndex;
use Spot\App\Web\Impl\Router\InvertedIndex\RegexPathIndex;
use Spot\App\Web\Impl\Router\InvertedIndex\StaticPathIndex;
use Spot\App\Web\Request;
use Spot\App\Web\Router;

abstract class InvertedIndexRouter extends AbstractRouter {
    private $actions,
            $method,
            $ajax,
            $staticPath,
            $prefixPath,
            $regexPath;

    public function __construct(
            array $names,
            array $actions,
            MethodIndex $method,
            AjaxIndex $ajax,
            StaticPathIndex $staticPath,
            PrefixPathIndex $prefixPath,
            RegexPathIndex $regexPath) {
        parent::__construct($names);

        $this->actions = $actions;
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
}
