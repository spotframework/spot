<?php
namespace Spot\App\Web\Impl\Router;

use Spot\App\Web\Request;
use Spot\App\Web\Router;

class InlineRouter extends AbstractRouter {
    private $actions,
            $compiler;

    public function __construct(
            array $names,
            array $actions,
            RoutePathCompiler $compiler) {
        parent::__construct($names);

        $this->actions = $actions;
        $this->compiler = $compiler;
    }

    function resolve(Request $request) {
        if(!$this->actions || !isset($this->actions[$request->method()])) {
            return;
        }

        $uri = $request->uri();
        $actions = $this->actions[$request->method()];
        foreach($actions as $route => $action) {
            if($this->compiler->checkSymbol($route)) {
                $pattern = $this->compiler->compile($route);
                if(preg_match($pattern, $uri, $paths)) {
                    foreach($paths as $name => $value) {
                        is_string($name) && !isset($request[$name]) &&
                            $request[$name] = $value;
                    }

                    return $action;
                }
            } else if($route == $request->uri()) {
                return $action;
            }
        }
    }
}
