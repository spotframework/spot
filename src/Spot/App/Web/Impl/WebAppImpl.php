<?php
namespace Spot\App\Web\Impl;

use Spot\App\Web\Request;
use Spot\App\Web\Response;
use Spot\App\Web\Router;
use Spot\App\Web\ScalarView;
use Spot\App\Web\WebApp;
use Spot\Inject\Named;

class WebAppImpl implements WebApp {
    private $router,
            $dispatcher,
            $renderers;

    public function __construct(
            Router $router,
            Dispatcher $dispatcher,
            /** @Named("app.web.view-renderers") */array $renderers = []) {
        $this->router = $router;
        $this->dispatcher = $dispatcher;
        $this->renderers = $renderers;
    }

    function handle(Request $request) {
        $response = new Response();

        $action = $this->router->resolve($request);
        if(empty($action)) {
            $response->setHttpCode(Response::NOT_FOUND);

            return $response;
        }

        $view = $this->dispatcher->dispatch($action, $request, $response);
        if($view !== null) {
            if(is_array($view) || is_scalar($view)) {
                $view = new ScalarView($view);
            }

            foreach($this->renderers as $renderer) {
                $renderer::rendererOf($view) &&
                    $renderer->render($view, $request, $response);
            }
        }

        return $response;
    }
}
