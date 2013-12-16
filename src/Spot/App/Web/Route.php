<?php
namespace Spot\App\Web;

/**
 * @Annotation
 * @Target({"METHOD", "CLASS"})
 */
class Route {
    public $value;

    public $ajax;

    public $method;

    public $name;
}
