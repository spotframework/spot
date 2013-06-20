<?php
namespace Spot\Domain;

class ValidationFailed extends \RuntimeException {
    private $errors;
    
    public function __construct(array $errors) {
        parent::__construct("");
        
        $this->errors = $errors;
    }
    
    public function getErrors() {
        return $this->errors;
    }
}