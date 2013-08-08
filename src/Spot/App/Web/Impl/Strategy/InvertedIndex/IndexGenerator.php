<?php
namespace Spot\App\Web\Impl\Strategy\InvertedIndex;

use Spot\App\Web\Impl\RoutePathCompiler;
use Spot\App\Web\Impl\MappingProvider;
use Spot\Inject\Named;

class IndexGenerator {
    private $hash,
            $namespaces,
            $mapping,
            $compiler;

    public function __construct(
            /** @Named("app.hash") */$hash,
            /** @Named("app.module.namespaces") */array $namespaces,
            MappingProvider $mapping,
            RoutePathCompiler $compiler) {
        $this->hash = $hash;
        $this->namespaces = $namespaces;
        $this->mapping = $mapping;
        $this->compiler = $compiler;
    }

    public function generate($className) {
        $staticUris = $regexPrefixes = $regexUris = $methods = $params = $actions = [];
        $ajax = [true => [], false => []];
        foreach($this->mapping->getMappings() as $i => $mapping) {
            $route = $mapping->getRoute();
            if(!preg_match('/(\*|\{|\()/', $route->value)) {
                $staticUris[$route->value][$i] = 1;
            } else {
                $pattern = $this->compiler->compile($route->value);

                $regexUris[$i] = '/^'.addcslashes($pattern, '/.').'$/';
                $regexPrefixes[substr($pattern, 0, strpos($pattern, '('))][$i] = 1;
            }

            foreach((array)$route->method as $method) {
                $methods[$method][$i] = 1;
            }

            if($route->ajax === null) {
                $ajax[true][$i] = $ajax[false][$i] = 1;
            } else {
                $ajax[$route->ajax][$i] = 1;
            }

            $actions[$i] = $mapping->getMethod();
        }

        return 'use Spot\App\Web\Impl\Strategy\InvertedIndex\AjaxRequestIndex;
use Spot\App\Web\Impl\Strategy\InvertedIndex\RequestMethodIndex;
use Spot\App\Web\Impl\Strategy\InvertedIndex\StaticUriIndex;
use Spot\App\Web\Impl\Strategy\InvertedIndex\RegexPrefixUriIndex;
use Spot\App\Web\Impl\Strategy\InvertedIndex\RegexUriIndex;
use Spot\App\Web\Impl\Strategy\InvertedIndex\ParamsIndex;
use Spot\App\Web\Impl\Strategy\InvertedIndex\InvertedIndexStrategy;

/**
 * Inverted index routing strategy of apps
 *      '.implode("\n *      ", $this->namespaces).'
 */
class '.$className.' extends InvertedIndexStrategy {
    static $actionMethods = '.var_export($actions, true).';

    public function __construct() {
        parent::__construct(
            new RequestMethodIndex__'.$this->hash.',
            new AjaxRequestIndex__'.$this->hash.',
            new StaticUriIndex__'.$this->hash.',
            new RegexPrefixUriIndex__'.$this->hash.',
            new RegexUriIndex__'.$this->hash.'
        );
    }
}

class AjaxRequestIndex__'.$this->hash.' extends AjaxRequestIndex {
    static $index = '.var_export($ajax, true).';
}

class RequestMethodIndex__'.$this->hash.' extends RequestMethodIndex {
    static $index = '.var_export($methods, true).';
}

class StaticUriIndex__'.$this->hash.' extends StaticUriIndex {
    static $index = '.var_export($staticUris, true).';
}

class RegexPrefixUriIndex__'.$this->hash.' extends RegexPrefixUriIndex {
    static $index = '.var_export($regexPrefixes, true).';
}

class RegexUriIndex__'.$this->hash.' extends RegexUriIndex {
    static $index = '.var_export($regexUris, true).';
}';
    }
}