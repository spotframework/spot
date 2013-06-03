<?php
namespace Spot\App\Cli\Impl;

use Spot\Code\CodeWriter;
use Spot\Code\Impl\CodeWriterImpl;
use Spot\Reflect\Method;
use Spot\Reflect\Parameter;

abstract class ActionGenerator {
    public function generate(Method $method, $className) {
        $writer = new CodeWriterImpl();
        
        $writer->writeln("use Spot\App\Cli\Args;");
        $writer->writeln("use Spot\Inject\Injector;");
        $writer->newLine();

        $writer->write("class CliAction__");
        $writer->write($className);
        $writer->write(" extends ActionAdapter {");
        $writer->indent();

            $writer->writeln('public $i;');
            $writer->newLine();

            $writer->write('function __construct(Injector $i) {');
            $writer->indent();
                $writer->write('$this->i = $i;');
            $writer->outdent();
            $writer->writeln("}");
            $writer->newLine();

            $writer->write('function execute(Args $args, Console $console) {');
            $writer->indent();
                $this->generateExecute($method, $writer);
            $writer->outdent();
            $writer->writeln("}");
            $writer->newLine();

            $writer->write('static function matches(Args $args) {');
            $writer->indent();
                $this->generateMatches($method, $method->getType()->getAnnotation("Spot\App\Cli\Command")->value, $writer);
            $writer->outdent();
            $writer->write("}");

        $writer->outdent();
        $writer->write("}");
        
        return $writer->getCode();
    }

    public function getOptionName(Parameter $parameter) {
        $option = $parameter->getAnnotation("Spot\App\Cli\Option");
        if($option && $option->value) {
            $name = $option->value;
        } else {
            $name = (strlen($parameter->name) === 1 ? "-" : "--").$parameter->name;
        }

        return $name;
    }

    public abstract function generateExecute(Method $method, CodeWriter $writer);

    public function generateMatches(Method $method, $command, CodeWriter $writer) {
        $writer->write("return ");
        $writer->indent();

        $writer->write('isset($args[0]) && ');
        $writer->writeValue($command);
        $writer->write(' === $args[0]');

        foreach($method->getParameters() as $parameter) {
            if($parameter->isDefaultValueAvailable()) {
                continue;
            }

            $writer->write(" && ");
            $writer->write('isset($args[');
            $writer->writeValue($this->getOptionName($parameter));
            $writer->write("])");
        }

        $writer->outdent();
        $writer->write(";");
    }
}