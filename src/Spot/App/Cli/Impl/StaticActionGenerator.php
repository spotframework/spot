<?php
namespace Spot\App\Cli\Impl;

use Spot\Reflect\Method;
use Spot\Code\CodeWriter;

class StaticActionGenerator extends ActionGenerator {
    public function generateExecute(Method $method, CodeWriter $writer) {
        $writer->write("return \\");
        $writer->write($method->getType()->name);
        $writer->write("::");
        $writer->write($method->name);
        $writer->write("(");

        $parameters = $method->getParameters();
        if($parameters) {
            $parameter = array_shift($parameters);
            $writer->write('$args[');
            $writer->writeValue($this->getOptionName($parameter));
            $writer->write(']');
            if(!$parameter->isArray()) {
                $writer->write('[0]');
            }

            foreach($parameters as $parameter) {
                $writer->write(', $args[');
                $writer->writeValue($this->getOptionName($parameter));
                $writer->write(']');
                if(!$parameter->isArray()) {
                    $writer->write('[0]');
                }
            }
        }
        $writer->write(");");
    }
}