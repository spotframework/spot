<?php
namespace Spot\App\Web;

use Spot\Http\Request;

interface WebApp {    
    /**
     * Transform request object into appropriate response object
     *
     * @return Response
     */
    function handle(Request $request);
}