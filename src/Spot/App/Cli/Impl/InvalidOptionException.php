<?php
namespace Spot\App\Cli\Impl;

class InvalidOptionException extends \RuntimeException {
    private $name,
            $optional,
            $array;
    
    public function __construct($name, $optional = false, $array = false) {
        parent::__construct();
        
        $this->name = $name;
        $this->optional = $optional;
        $this->array = $array;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function isOptional() {
        return $this->optional;
    }
    
    public function isArray() {
        return $this->array;
    }
}
