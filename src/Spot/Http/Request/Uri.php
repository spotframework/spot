<?php
namespace Spot\Http\Request;

class Uri {
    private $uri;
    
    public function __construct($uri) {
        $this->uri = '/'.trim(urldecode($uri), '/');
    }
    
    public function __toString() {
        return $this->uri;
    }
}