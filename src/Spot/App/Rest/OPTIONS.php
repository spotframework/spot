<?php
namespace Spot\App\Rest;

use Spot\Http\Request;
use Spot\App\Rest\Impl\RequestMethod;

/** @Annotation */
final class OPTIONS implements RequestMethod {
    public function __toString() {
        return Request::OPTIONS;
    }    
}