<?php
namespace Spot\App\Web\Impl;

use Spot\Reflect\Method;
use Spot\Code\CodeWriter;

class InstanceActionGenerator extends AbstractActionGenerator {
    private $extractor;
    
    public function __construct(BindingExtractor $extractor) {
        $this->extractor = $extractor;
    }
    
    public function generateBody(Method $method, CodeWriter $writer) {
        $writer->write('return ');
        if($method->getType()->getConstructor()) {
            $writer->write('$this->i->getInstance(');
            $writer->writeValue($method->getType()->name);
        } else {
            $writer->write('(new \\');
            $writer->write($method->getType()->name);
        }
        
        $writer->write(')->');
        $writer->write($method->name);
        $writer->write('(');
        
        $bindings = $this->extractor->extract($method);
        if(!empty($bindings)) {
            $writer->indent();
            
            array_shift($bindings)->compile($writer);
            foreach($bindings as $binding) {
                $writer->write(',');
                $writer->newLine();
                
                $binding->compile($writer);
            }
            
            $writer->outdent();
        }
        
        $writer->write(');');
    }    
}