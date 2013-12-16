<?php
namespace Spot\Gen;

class CodeWriter {
    private $code,
            $indentation = 0;

    public function write($code) {
        $this->code .= implode(func_get_args());
    }

    public function nl() {
        $this->write(PHP_EOL, str_repeat("    ", $this->indentation));
    }

    public function writeln($code) {
        $this->code .= implode(func_get_args());
        $this->nl();
    }

    public function printf($format) {
        $this->write(call_user_func_array("sprintf", func_get_args()));
    }

    public function literal($value) {
        if(is_string($value)) {
            $this->write('"', $value, '"');
        } else if(is_numeric($value)) {
            $this->write($value);
        } else if(is_array($value)) {
            $this->write("[");
            foreach($value as $k => $v) {
                $this->literal($k);
                $this->write(" => ");
                $this->literal($v);
            }
            $this->write("]");
        }  else if(is_null($value)) {
            $this->write("null");
        } else if(is_bool($value)) {
            $value ? $this->write("true") : $this->write("false");
        } else if($value instanceof \stdClass) {
            $this->write("(object)");
            $this->literal((array)$value);
        } else {
            throw new \InvalidArgumentException("Unsupported value given, supported values are scalar, array and \\stdClass instance, ".gettype($value)." given");
        }
    }

    public function indent($step = 1) {
        $this->indentation += $step;

        $this->nl();
    }

    public function outdent($step = 1) {
        if($this->indentation - $step < 0) {
            throw new \InvalidArgumentException("Outdenting by {$step} step will result in negative indentation");
        }

        $this->indentation -= $step;

        $this->nl();
    }

    public function __toString() {
        return $this->code;
    }

    static public function create() {
        return new CodeWriter();
    }
}
