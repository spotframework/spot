<?php
namespace Spot\Domain\Impl\Binding;

use Spot\Domain\Impl\Binding;
use Spot\Code\CodeWriter;

class ValueBinding implements Binding {
    private $name;
    
    public function __construct($name) {
        $this->name = $name;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function compile(CodeWriter $writer) {
        $writer->write('$b[');
        $writer->writeValue($this->name);
        $writer->write(']');
    }
}