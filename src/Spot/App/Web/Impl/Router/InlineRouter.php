<?php
namespace Spot\App\Web\Impl\Router;

use Spot\App\Web\Request;
use Spot\App\Web\Router;

class InlineRouter implements Router {
    private $actions,
            $compiler;

    public function __construct(
            array $actions,
            RoutePathCompiler $compiler) {
        $this->actions = $actions;
        $this->compiler = $compiler;
    }

    function resolve(Request $request) {
        if($this->actions && isset($this->actions[$request->method()])) {
            $uri = $request->uri();
            $actions = $this->actions[$request->method()];
            foreach($actions as $route => $action) {
                if($this->compiler->checkSymbol($route)) {
                    $pattern = $this->compiler->compile($route);
                    if(preg_match($pattern, $uri, $matches)) {
                        $request->setPaths($matches);

                        return $action;
                    }
                } else if($route == $request->uri()) {
                    return $action;
                }
            }
        }
    }
}
