<?php
namespace Spot\App\REST;


use Spot\App\REST\Impl\RequestMethod;
use Spot\App\Web\Request;

/** @Annotation */
class POST implements RequestMethod {
    function __toString() {
        return Request::POST;
    }
}
