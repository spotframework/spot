<?php
namespace Spot\App\Web\Impl\Binding;

use Spot\Code\CodeWriter;
use Spot\App\Web\Impl\Binding;

class OptionalBinding implements Binding {
    private $check,
            $binding,
            $value;
    
    public function __construct(Binding $check, Binding $binding, $value) {
        $this->check = $check;
        $this->binding = $binding;
        $this->value = $value;
    }
    
    public function compile(CodeWriter $writer) {
        $writer->write("isset(");
        $this->check->compile($writer);
        $writer->write(") ? ");
        $this->binding->compile($writer);
        $writer->write(' : ');
        $writer->writeValue($this->value);
    }    
}