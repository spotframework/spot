<?php
namespace Spot\App\REST;

use Spot\App\REST\Impl\RequestMethod;
use Spot\App\Web\Request;

/** @Annotation */
class PUT implements RequestMethod {
    function __toString() {
        return Request::PUT;
    }
}
