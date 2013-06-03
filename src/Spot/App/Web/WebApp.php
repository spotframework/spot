<?php
namespace Spot\App\Web;

/**
 *
 * @package Spot\App\Web
 */
interface WebApp {
    const VIEW_RENDERERS = "webapp.view-renderers";
    
    /**
     * Produce response from current configured request
     *
     * @return Response
     */
    function handleRequest();
}