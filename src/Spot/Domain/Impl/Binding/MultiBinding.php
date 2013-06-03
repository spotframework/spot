<?php
namespace Spot\Domain\Impl\Binding;

use Spot\Domain\Impl\Binding;
use Spot\Code\CodeWriter;

class MultiBinding implements Binding {
    private $bindings,
            $binding,
            $method;
    
    public function __construct(ValueBinding $bindings, Binding $binding, $method) {
        $this->bindings = $bindings;
        $this->binding = $binding;
        $this->method = $method;
    }
    
    public function compile(CodeWriter $writer) {
        $writer->write('foreach((array)');
        $this->bindings->compile($writer);
        $writer->write(' as $v) {');
        $writer->indent();
        $writer->write('$i->');
        $writer->write($this->method);
        $writer->write('(');
        $this->binding->compile($writer);
        $writer->write(');');
        $writer->outdent();
        $writer->write('}');
    }    
}