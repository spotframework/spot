<?php
namespace Spot\Http\Request;

class UserAgent {
    private $ua;
    
    public function __construct($ua) {        
        $this->ua = $ua;
    }
    
    public function __toString() {
        return $this->ua;
    }
}