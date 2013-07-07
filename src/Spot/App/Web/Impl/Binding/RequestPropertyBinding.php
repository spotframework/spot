<?php
namespace Spot\App\Web\Impl\Binding;

use Spot\Code\CodeWriter;
use Spot\App\Web\Impl\Binding;

class RequestPropertyBinding implements Binding {
    private $name;
    
    public function __construct($name) {
        $this->name = $name;
    }
    
    public function compile(CodeWriter $writer) {
        $writer->write('$rq->');
        $writer->write($this->name);
    }    
}