<?php
namespace Spot\Domain\Impl\Binding;

use Spot\Domain\Impl\Binding;
use Spot\Code\CodeWriter;

class InstantiationBinding implements Binding {
    private $className,
            $parameters;
    
    public function __construct($className, array $parameters) {
        $this->className = $className;
        $this->parameters = $parameters;
    }
    
    public function compile(CodeWriter $writer) {
        $writer->write('new \\');
        $writer->write($this->className);
        $writer->write('(');
        if(!empty($this->parameters)) {
            $parameters = $this->parameters;
            array_shift($parameters)->compile($writer);
            foreach($parameters as $parameter) {
                $writer->write(', ');
                $parameter->compile($writer);
            }
        }
        $writer->write(');');
    }    
}