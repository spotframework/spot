<?php
namespace Spot\Code\Impl;

class ConciseCodeWriter extends CodeWriterImpl {
    public function indent($step = 1) {
        $this->write(' ');
    }
    
    public function outdent($step = 1) {
        $this->write(' ');
    }
    
    public function newLine() {
        $this->write(' ');
    }
}