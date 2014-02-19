<?php
namespace Spot\Inject\Impl;

use Spot\Gen\CodeWriter;
use Spot\Reflect\Parameter;
use Spot\Reflect\Type;
use Spot\Reflect\Method;

class LazyGenerator {
    public function generate(Type $type, CodeWriter $writer) {
        $writer->writeln('public $i, $k, $d;');
        $writer->write('function __construct(Spot\Inject\Injector $i, Spot\Inject\Key $k) {');
        $writer->indent();
            $writer->write('$this->i = $i;');
            $writer->write('$this->k = $k;');
        $writer->outdent();
        $writer->writeln('}');

        $writer->write("function __() {");
        $writer->indent();
            $writer->write('return $this->d ?: $this->d = $this->i->get($this->k);');
        $writer->outdent();
        $writer->write("}");

        foreach($type->getMethods(Method::IS_PUBLIC) as $method) {
            if(!$method->isConstructor()) {
                $this->generateMethod($method, $writer);
            }
        }
    }

    public function generateMethod(Method $method, CodeWriter $writer) {
        $writer->write("function ", $method->name, " (");
        $parameters = $method->getParameters();
        if($parameters) {
            $this->generateParameter(array_shift($parameters), $writer);
            foreach($method->getParameters() as $parameter) {
                $this->generateParameter($parameter, $writer);
            }
        }
        $writer->write(") {");
        $writer->indent();
            $writer->write('return $this->__(');
            $parameters = $method->getParameters();
            if($parameters) {
                $writer->write("$", array_shift($parameters)->name);
                foreach($method->getParameters() as $parameter) {
                    $writer->write("$", $parameter->name);
                }
            }
            $writer->write(");");
        $writer->outdent();
        $writer->write("}");
    }

    public function generateParameter(Parameter $parameter, CodeWriter $writer) {
        $class = $parameter->getClass();
        if($class) {
            $writer->write($class->name, " ");
        } else if($parameter->isArray()) {
            $writer->write("array ");
        } else if($parameter->isCallable()) {
            $writer->write("callable ");
        }

        $writer->write("$", $parameter->name);
    }
}
