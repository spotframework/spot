<?php
namespace Spot\App\REST;

use Spot\App\REST\Impl\RequestMethod;
use Spot\App\Web\Request;

/** @Annotation */
class DELETE implements RequestMethod {
    function __toString() {
        return Request::DELETE;
    }
}
