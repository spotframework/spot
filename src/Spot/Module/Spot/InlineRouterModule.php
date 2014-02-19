<?php
namespace Spot\Module\Spot;

use Spot\App\Web\Impl\Router\RoutePathCompiler;
use Spot\App\Web\Request;
use Spot\Inject\Provides;
use Spot\Inject\Singleton;
use Spot\App\Web\Impl\Router\InlineRouter;

class InlineRouterModule {
    private $names = [],
            $actions = [];

    public function route($method, $path, $action, $name) {
        $this->actions[$method][$path] = $action;
        $name &&
            ($this->names[$name] = $path);

        return $this;
    }

    public function get($path, $action, $name = null) {
        return $this->route(Request::GET, $path, $action, $name);
    }

    public function put($path, $action, $name = null) {
        return $this->route(Request::PUT, $path, $action, $name);
    }

    public function post($path, $action, $name = null) {
        return $this->route(Request::POST, $path, $action, $name);
    }

    public function delete($path, $action, $name = null) {
        return $this->route(Request::DELETE, $path, $action, $name);
    }

    /** @Provides("Spot\App\Web\Router", overrides = true) @Singleton */
    public function provideRouter(RoutePathCompiler $compiler) {
        return new InlineRouter($this->names, $this->actions, $compiler);
    }
}
