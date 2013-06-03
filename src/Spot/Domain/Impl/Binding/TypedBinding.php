<?php
namespace Spot\Domain\Impl\Binding;

use Spot\Domain\Impl\Binding;
use Spot\Code\CodeWriter;

class TypedBinding implements Binding {
    private $value,
            $typeName;
    
    public function __construct(Binding $value, $typeName) {
        $this->value = $value;
        $this->typeName = $typeName;
    }
    
    public function compile(CodeWriter $writer) {
        $writer->write('is_array(');
        $this->value->compile($writer);
        $writer->write(') ? $this->d->newInstance(');
        $writer->writeValue($this->typeName);
        $writer->write(', ');
        $this->value->compile($writer);
        $writer->write(') : $this->d->find(');
        $writer->writeValue($this->typeName);
        $writer->write(', ');
        $this->value->compile($writer);
        $writer->write(')');
    }    
}