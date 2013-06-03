<?php
namespace Spot\App\Web\Impl;

use Spot\App\Web\View;
use Spot\App\Web\WebApp;
use Spot\Http\Request;
use Spot\Http\Response;
use Spot\Inject\Named;

class WebAppImpl implements WebApp {
    private $request,
            $response, 
            $router,
            $renderers;
    
    public function __construct(
            Request $request,
            Response $response,
            Router $router,
            /** @Named(WebApp::VIEW_RENDERERS) */array $renderers = []) {
        $this->request = $request;
        $this->response = $response;
        $this->router = $router;
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