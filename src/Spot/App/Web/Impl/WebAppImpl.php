<?php
namespace Spot\App\Web\Impl;

use Spot\Http\Request;
use Spot\Http\Response;
use Spot\App\Web\WebApp;
use Spot\Inject\Injector;
use Spot\App\Web\View;
use Spot\App\Web\Impl\RequestModule;
use Spot\Inject\Key;
use Spot\Inject\Named;
use Spot\App\Web\Impl\RoutingNotFound;

class WebAppImpl implements WebApp {
    private $router,
            $renderers;
    
    public function __construct(
            Router $router,
            /** @Named("app.web.view-renderers") */array $renderers = []) {
        $this->router = $router;
        $this->renderers = $renderers;
    }
    
    public function handle(Request $request) {
        $response = new Response();
        
        try {
            $action = $this->router->resolve($request);
            $view = $action->invoke($request, $response);
            if($view) {
                if(is_scalar($view) || is_array($view)) {
                    $view = new ScalarView($view);
                }

                if($view instanceof View) {                    
                    foreach($this->renderers as $renderer) {
                        $renderer::rendererOf($view) &&
                            $renderer->render($view, $request, $response);
                    }
                }
            }
        } catch(RoutingNotFound $e) {
            $response->setHttpCode(Response::NOT_FOUND);
        }
        
        return $response;
    }
}