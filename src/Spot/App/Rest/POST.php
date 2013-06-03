<?php
namespace Spot\App\Rest;

use Spot\Http\Request;
use Spot\App\Rest\Impl\RequestMethod;

/** @Annotation */
final class POST implements RequestMethod {
    public function __toString() {
        return Request::POST;
    }    
}