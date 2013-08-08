<?php
namespace Spot\App\Web\Impl\Strategy\InvertedIndex;

use Spot\App\Web\Impl\RoutingNotFound;
use Spot\App\Web\Impl\RoutingStrategy;
use Spot\Http\Request;

abstract class InvertedIndexStrategy implements RoutingStrategy {
    private $method,
            $ajax,
            $staticUri,
            $regexPrefixUri,
            $regexUri;

    public function __construct(
            RequestMethodIndex $method,
            AjaxRequestIndex $ajax,
            StaticUriIndex $staticUri,
            RegexPrefixUriIndex $regexPrefixUri,
            RegexUriIndex $regexUri) {
        $this->method = $method;
        $this->ajax = $ajax;
        $this->staticUri = $staticUri;
        $this->regexPrefixUri = $regexPrefixUri;
        $this->regexUri = $regexUri;
    }

    public function resolve(Request $request) {
        $candidates = array_intersect_key(
            static::$actionMethods,
            $this->staticUri->matches($request)
        );

        if($candidates) {
            $candidates = array_intersect_key(
                $candidates,
                $methods = $this->method->matches($request),
                $ajax = $this->ajax->matches($request)
            );

            if($candidates) {
                return explode("::", reset($candidates));
            }
        }

        $candidates = array_intersect_key(
            static::$actionMethods,
            isset($methods) ? $methods : $this->method->matches($request),
            isset($ajax) ? $ajax : $this->ajax->matches($request)
        );

        $candidates && $candidates = array_intersect_key(
            $candidates,
            $this->regexPrefixUri->matches($request, $candidates)
        );

        $candidates && $candidates = array_intersect_key(
            $candidates,
            $this->regexUri->matches($request, $candidates)
        );

        if($candidates) {
            return explode("::", reset($candidates));
        }

        throw new RoutingNotFound();
    }
}