<?php
namespace Spot\Domain\Impl\Binding;

use Spot\Domain\Impl\Binding;
use Spot\Code\CodeWriter;

class VarBinding implements Binding {
    private $name;
    
    public function __construct($name) {
        $this->name = $name;
    }
    
    public function compile(CodeWriter $writer) {
        $writer->write('$');
        $writer->write($this->name);
    }    
}