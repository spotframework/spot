<?php
namespace Spot\App\Web\Impl;

use Spot\Http\Request;
use Spot\Http\Response;
use Spot\Inject\Named;
use Spot\App\Web\WebApp;

class RequestHandler {
    private $router,
            $request,
            $response,
            $renderers;
    
    public function __construct(
            Router $router,
            Request $request, 
            Response $response,
            /** @Named(WebApp::VIEW_RENDERERS) */array $renderers = []) {
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
                            $renderer->render($view, $this->response);
                    }
                } else {
                    $this->response->setHttpCode(Response::INTERNAL_SERVER_ERROR);
                }
            }
        } catch(RoutingNotFound $e) {
            $this->response->setHttpCode(Response::NOT_FOUND);
        } catch(\Exception $e) {
            $this->response->setHttpCode(Response::INTERNAL_SERVER_ERROR);
        }
        
        return $this->response;
    }
}