<?php
namespace Spot\App\Rest;

use Spot\Http\Request;
use Spot\App\Rest\Impl\RequestMethod;

/** @Annotation */
final class PUT implements RequestMethod {
    public function __toString() {
        return Request::PUT;
    }    
}