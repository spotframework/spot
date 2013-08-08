<?php
namespace Spot\App\Web;

/** @Annotation */
class Route {
    public $value;
    
    /**
     * Request method(s)
     * 
     * @var mixed
     */
    public $method;

    /** @var boolean */
    public $ajax;

    /**
     * Required params
     *
     * @var array
     */
    public $params = [];

    /**
     * Optional route name
     *
     * @var string
     */
    public $name;
}