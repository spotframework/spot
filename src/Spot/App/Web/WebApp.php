<?php
namespace Spot\App\Web;

interface WebApp {
    /**
     * @param Request $request
     * @return Response
     */
    function handle(Request $request);
}
