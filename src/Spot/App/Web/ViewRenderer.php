<?php
namespace Spot\App\Web;

interface ViewRenderer {
    /**
     * @param View $view
     * @return boolean
     */
    static function rendererOf(View $view);

    function render(View $view, Request $request, Response $response);
}
