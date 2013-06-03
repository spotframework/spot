<?php
namespace Spot\Code\Impl;

use Spot\Code\CodeWriter;

class CodeWriterImpl implements CodeWriter {
    private $indent = 0,
            $tab = '    ',
            $code = '';
    
    public function indent($step = 1) {
        $this->indent += $step;
        
        $this->newLine();
    }

    public function newLine() {
        $this->write("\n");
        $this->write(str_repeat($this->tab, $this->indent));
    }

    public function outdent($step = 1) {
        $this->indent -= $step;
        if($this->indent < 0) {
            throw new \RuntimeException('Outdent failed since indentation negative');
        }
        
        $this->newLine();
    }

    public function write($code) {
        $this->code .= $code;
    }
    
    public function writeln($code) {
        $this->write($code);
        $this->newLine();
    }

    public function writeValue($value) {
        if(is_object($value)) {
            throw new \InvalidArgumentException('Value must be scalar or array');
        }
        
        if(is_string($value)) {
            $this->write('"'.$value.'"');
        } else if(is_numeric($value)) {
            $this->write($value);
        } else if(is_array($value)) {
            $this->write("[");
            foreach($value as $k => $v) {
                $this->writeValue($k);
                $this->write(" => ");
                $this->writeValue($v);
                $this->write(", ");
            }
            $this->write("]");
        } else if(is_null($value)) {
            $this->write("null");
        } else if(is_bool($value)) {
            $value ? $this->write("true") : $this->write("false");
        }
    }
    
    public function getCode() {
        return $this->code;
    }
    
    public function __tostring() {
        return $this->getCode();
    }
}