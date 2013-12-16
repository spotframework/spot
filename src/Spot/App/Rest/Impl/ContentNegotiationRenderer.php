<?php
namespace Spot\App\REST\Impl;

use Spot\App\Web\Request;
use Spot\App\Web\Response;
use Spot\App\Web\ScalarView;
use Spot\App\Web\View;
use Spot\App\Web\ViewRenderer;

class ContentNegotiationRenderer implements ViewRenderer {
    function render(View $view, Request $request, Response $response) {
        $response->write(json_encode($view->getModel()), "\n");
    }

    static function rendererOf(View $view) {
        return $view instanceof ScalarView;
    }
}
