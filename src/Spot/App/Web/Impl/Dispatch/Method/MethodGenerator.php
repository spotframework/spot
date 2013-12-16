<?php
namespace Spot\App\Web\Impl\Dispatch\Method;

use Spot\Gen\CodeWriter;
use Spot\Reflect\Method;
use Spot\Reflect\Parameter;

abstract class MethodGenerator {
    public function generate(Method $method, CodeWriter $writer) {
        $writer->write('static function invoke($i, $d, $rq, $rp) {');
        $writer->indent();
            $writer->write("return ");
            $this->generateMethodCall($method, $writer);
            $writer->write("(");
            $parameters = $method->getParameters();
            if($parameters) {
                $this->generateParameter(array_shift($parameters), $writer);
                foreach($parameters as $parameter) {
                    $writer->writeln(', ');
                    $this->generateParameter($parameter, $writer);
                }
            }
            $writer->write(");");
        $writer->outdent();
        $writer->write("}");
    }

    public function generateParameter(Parameter $parameter, CodeWriter $writer) {
        $name = $parameter->isAnnotatedWith("Spot\\App\\Web\\Param")
            ? $parameter->getAnnotation("Spot\\App\\Web\\Param")->value
            : $parameter->name;

        if($parameter->isDefaultValueAvailable()) {
            $writer->write("isset(");
            $this->generateBindedParameter($name, $writer);
            $writer->write(") ? ");
        }

        $class = $parameter->getClass();
        if($class) {
            if($class->name == "Spot\\App\\Web\\Request") {
                $writer->write('$rq');
            } else if($class->name == "Spot\\App\\Web\\Response") {
                $writer->write('$rp');
            } else {
                $writer->write('is_array(');
                $this->generateBindedParameter($name, $writer);
                $writer->write(') ? $d->newInstance(');
                $writer->literal($class->name);
                $writer->write(', ');
                $this->generateBindedParameter($name, $writer);
                $writer->write(') : $d->find(');
                $writer->literal($class->name);
                $writer->write(', ');
                $this->generateBindedParameter($name, $writer);
                $writer->write(')');
            }
        } else {
            $this->generateBindedParameter($name, $writer);
        }

        if($parameter->isDefaultValueAvailable()) {
            $writer->write(" : ");
            $parameter->isDefaultValueConstant()
                ? $writer->write($parameter->getDefaultValueConstantName())
                : $writer->literal($parameter->getDefaultValue());
        }
    }

    public function generateBindedParameter($name, CodeWriter $writer) {
        if(empty($name)) {
            $writer->write('iterator_to_array($rq)');
        } else {
            $writer->write('$rq[');
            $writer->literal($name);
            $writer->write(']');
        }
    }

    public abstract function generateMethodCall(Method $method, CodeWriter $writer);
}
