<?php
namespace Spot\App\Rest\Impl;

use Spot\Http\Request;
use Spot\Http\Response;
use Spot\App\Web\View;
use Spot\App\Web\ViewRenderer;
use Spot\App\Web\Impl\ScalarView;

class ContentNegotiationRenderer implements ViewRenderer {
    private $request;
    
    public function __construct(Request $request) {
        $this->request = $request;
    }
    
    public function render(View $view, Response $response) {
        //TODO:implements
    }

    static public function rendererOf(View $view) {
        return $view instanceof ScalarView;
    }
}