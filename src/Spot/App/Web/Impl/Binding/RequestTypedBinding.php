<?php
namespace Spot\App\Web\Impl\Binding;

use Spot\Code\CodeWriter;
use Spot\App\Web\Impl\Binding;

class RequestTypedBinding implements Binding {
    private $className;
    
    public function __construct($className) {
        $this->className = $className;
    }
    
    public function compile(CodeWriter $writer) {
        $writer->write('$this->d->newInstance(');
        $writer->writeValue($this->className);
        $writer->write(', iterator_to_array($rq))');
    }    
}