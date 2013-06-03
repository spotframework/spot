<?php
namespace Spot\App\Web;

use Spot\Http\Response;

interface ViewRenderer {
    /**
     * @param View $view
     * @param Response $response
     * @return null
     */
    function render(View $view, Response $response);

    /**
     * @param View $view
     * @return boolean
     */
    static function rendererOf(View $view);
}