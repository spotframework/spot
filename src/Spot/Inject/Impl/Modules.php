<?php
namespace Spot\Inject\Impl;

use ArrayIterator;
use IteratorAggregate;

class Modules implements IteratorAggregate {
    private $modules = [],
            $hash;
    
    public function __construct(array $modules) {        
        $this->modules = array_values(array_unique($modules));
    }
    
    public function hash() {        
        return 
            $this->hash ?: 
            $this->hash = md5(implode($this->modules));
    }
    
    public function getIterator() {
        return new ArrayIterator($this->modules);
    }
}
