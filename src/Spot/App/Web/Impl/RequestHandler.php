<?php
namespace Spot\App\Web\Impl;

use Spot\Http\Request;
use Spot\Http\Response;
use Spot\Inject\Named;
use Spot\App\Web\WebApp;
use Spot\App\Web\View;

class RequestHandler {
    private $router,
            $request,
            $response,
            $renderers;
    
    public function __construct(
            Router $router,
            Request $request, 
            Response $response,
            /** @Named("app.web.view-renderers") */array $renderers = []) {
        $this->router = $router;
        $this->request = $request;
        $this->response = $response;
        $this->renderers = $renderers;
    }
    
    public function handleRequest() {
        try {
            $action = $this->router->resolve($this->request);
            $view = $action->invoke($this->request, $this->response);
            if($view) {
                if(is_scalar($view) || is_array($view)) {
                    $view = new ScalarView($view);
                }

                if($view instanceof View) {
                    foreach($this->renderers as $renderer) {
                        $renderer::rendererOf($view) &&
                            $renderer->render($view, $this->request, $this->response);
                    }
                }
            }
        } catch(RoutingNotFound $e) {
            $this->response->setHttpCode(Response::NOT_FOUND);
        }
        
        return $this->response;
    }
}