<?php
namespace Spot\Http\Request;

class Uri {
    private $uri;
    
    public function __construct($uri) {
        $this->uri = '/'.ltrim(urldecode($uri), '/');
    }
    
    public function __toString() {
        return $this->uri;
    }
}