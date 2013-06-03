<?php
namespace Spot\App\Web\Impl;

use Spot\Http\Request;
use Spot\Http\Response;
use Spot\Inject\Provides;
use Spot\Inject\Singleton;

class RequestModule {
    /** @Provides("Spot\Http\Request") @Singleton */
    static function provideRequest() {
        return Request::createFromGlobal();
    }
    
    /** @Provides("Spot\Http\Response") @Singleton */
    static function provideResponse(Response $response) {
        return $response;
    }
}