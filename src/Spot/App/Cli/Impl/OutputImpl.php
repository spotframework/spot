<?php
namespace Spot\App\Cli\Impl;

use Spot\App\Cli\Output;

class OutputImpl implements Output {    
    private $buffer;
    
    public function flush() {
        echo $this->buffer;
    }

    public function printf($format) {
        $this->buffer .= call_user_func_array("sprintf", func_get_args());
    }

    public function write($output) {
        $this->buffer .= implode(func_get_args());
    }

    public function writeln($output) {
        $this->write(implode(func_get_args()));
        $this->buffer .= PHP_EOL;
    }    
    
    public function clear() {
        $b = $this->buffer;
        
        $this->buffer = "";
        
        return $b;
    }
}