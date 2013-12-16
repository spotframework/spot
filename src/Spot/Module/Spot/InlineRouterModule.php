<?php
namespace Spot\Module\Spot;

use Spot\App\Web\Impl\Router\RoutePathCompiler;
use Spot\App\Web\Request;
use Spot\Inject\Provides;
use Spot\Inject\Singleton;
use Spot\App\Web\Impl\Router\InlineRouter;

class InlineRouterModule {
    private $actions = [];

    public function route($method, $path, \Closure $action) {
        $this->actions[$method][$path] = $action;

        return $this;
    }

    public function get($path, \Closure $action) {
        return $this->route(Request::GET, $path, $action);
    }

    public function put($path, \Closure $action) {
        return $this->route(Request::PUT, $path, $action);
    }

    public function post($path, \Closure $action) {
        return $this->route(Request::POST, $path, $action);
    }

    public function delete($path, \Closure $action) {
        return $this->route(Request::DELETE, $path, $action);
    }

    /** @Provides("Spot\App\Web\Router", overrides = true) @Singleton */
    public function provideRouter(RoutePathCompiler $compiler) {
        return new InlineRouter($this->actions, $compiler);
    }
}
