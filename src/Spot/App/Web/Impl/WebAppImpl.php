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
            $renderers,

            $responseCode,
            $responseHeaders,
            $httpVersion;

    public function __construct(
            Router $router,
            Dispatcher $dispatcher,
            /** @Named("app.web.view-renderers") */array $renderers = [],
            /** @Named("app.web.response.code") */$responseCode = Response::OK,
            /** @Named("app.web.response.headers") */$responseHeaders = [],
            /** @Named("app.web.response.http-version") */$httpVersion = "HTTP/1.1") {
        $this->router = $router;
        $this->dispatcher = $dispatcher;
        $this->renderers = $renderers;

        $this->responseCode = $responseCode;
        $this->responseHeaders = $responseHeaders;
        $this->httpVersion = $httpVersion;
    }

    function handle(Request $request) {
        $response = new Response(
            $this->responseCode,
            $this->responseHeaders,
            $this->httpVersion
        );

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
