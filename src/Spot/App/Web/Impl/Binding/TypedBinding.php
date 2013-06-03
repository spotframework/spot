<?php
namespace Spot\App\Web\Impl\Binding;

use Spot\Code\CodeWriter;
use Spot\App\Web\Impl\Binding;

class TypedBinding implements Binding {
    private $value,
            $className;
    
    public function __construct(
            Binding $value,
            $className) {
        $this->value = $value;
        $this->className = $className;
    }

    public function compile(CodeWriter $writer) {
        $writer->write('is_array(');
        $this->value->compile($writer);
        $writer->write(') ? $this->d->newInstance(');
        $writer->writeValue($this->className);
        $writer->write(', ');
        $this->value->compile($writer);
        $writer->write(') : $this->d->find(');
        $writer->writeValue($this->className);
        $writer->write(', ');
        $this->value->compile($writer);
        $writer->write(')');
    }
}