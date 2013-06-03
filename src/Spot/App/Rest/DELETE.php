<?php
namespace Spot\App\Rest;

use Spot\Http\Request;
use Spot\App\Rest\Impl\RequestMethod;

/** @Annotation */
final class DELETE implements RequestMethod {
    public function __toString() {
        return Request::DELETE;
    }    
}