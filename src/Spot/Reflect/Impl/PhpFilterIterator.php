<?php
namespace Spot\Reflect\Impl;

class PhpFilterIterator extends \FilterIterator {
    public function accept() {
        $file = $this->current();
        
        return 
            $file->getExtension() === 'php';
    }    
}