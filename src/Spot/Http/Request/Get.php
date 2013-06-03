<?php
namespace Spot\Http\Request;

class Get extends Vars {    
    public function __toString() {
        return (count($this) ? '?' : '').http_build_query($this);
    }
}