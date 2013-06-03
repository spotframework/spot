<?php
namespace Spot\App\Web\Impl;

use Spot\Http\Request;
use Spot\Http\Response;
use Spot\Inject\Provides;
use Spot\Inject\Singleton;

class RequestModule {
    private $request;
    
    public function __construct(Request $request) {
        $this->request = $request;
    }
    
    /** @Provides("Spot\Http\Request") @Singleton */
    function provideRequest() {
        return $this->request;
    }
    
    /** @Provides("Spot\Http\Response") @Singleton */
    static function provideResponse(Response $response) {
        return $response;
    }
}