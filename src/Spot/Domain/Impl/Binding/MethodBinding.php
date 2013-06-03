<?php
namespace Spot\Domain\Impl\Binding;

use Spot\Domain\Impl\Binding;
use Spot\Code\CodeWriter;

class MethodBinding implements Binding {
    private $check,
            $value,
            $method;
    
    public function __construct(
            Binding $check, 
            Binding $value, 
            $method) {
        $this->check = $check;
        $this->value = $value;
        $this->method = $method;
    }
    
    public function compile(CodeWriter $writer) {
        $writer->write('isset(');
        $this->check->compile($writer);
        $writer->write(') && ');
        $writer->indent();
        $writer->write('$i->');
        $writer->write($this->method);
        $writer->write('(');
        $this->value->compile($writer);
        $writer->write(');');
        $writer->outdent();
    }    
}