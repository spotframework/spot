<?php
namespace Spot\App\Web;

use Spot\Http\Request;
use Spot\Http\Response;

interface ViewRenderer {
    /**
     * @param View $view
     * @param Request $request
     * @param Response $response
     * @return null
     */
    function render(View $view, Request $request, Response $response);

    /**
     * @param View $view
     * @return boolean
     */
    static function rendererOf(View $view);
}