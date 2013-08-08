<?php
namespace Spot\App\Web\Impl;

use Spot\Http\Request;
use Spot\Http\Response;
use Spot\Inject\Provides;
use Spot\Inject\Singleton;

class RequestModule {
    private $request,
            $response;
    
    public function __construct(Request $request, Response $response) {
        $this->request = $request;
        $this->response = $response;
    }
    
    /** @Provides("Spot\Http\Request") @Singleton */
    function provideRequest() {
        return $this->request;
    }
    
    /** @Provides("Spot\Http\Request\Cookie") @Singleton */
    static function provideCookie(Request $request) {
        return $request->cookie;
    }
    
    /** @Provides("Spot\Http\Response") @Singleton */
    function provideResponse() {
        return $this->response;
    }
}