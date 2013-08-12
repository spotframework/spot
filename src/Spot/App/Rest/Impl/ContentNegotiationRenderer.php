<?php
namespace Spot\App\Rest\Impl;

use Spot\Http\Request;
use Spot\Http\Response;
use Spot\App\Web\View;
use Spot\App\Web\ViewRenderer;
use Spot\App\Web\Impl\ScalarView;

class ContentNegotiationRenderer implements ViewRenderer {    
    public function render(View $view, Request $request, Response $response) {
        $response->setContentType("application/json");
        echo json_encode($view->getModel()), "\n";
    }

    static public function rendererOf(View $view) {
        return $view instanceof ScalarView;
    }
}