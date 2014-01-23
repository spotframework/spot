<?php
namespace Spot\Inject\Impl\Aspect;

use Spot\Gen\CodeWriter;
use Spot\Inject\Impl\BindingLocator;
use Spot\Inject\Impl\Visitors\FactoryCompilerVisitor;
use Spot\Reflect\Method;
use Spot\Reflect\Parameter;
use Spot\Reflect\Type;

class ProxyGenerator {
    private $pointCuts,
            $locator,
            $aspect;

    public function __construct(
            PointCuts $pointCuts,
            BindingLocator $locator) {
        $this->pointCuts = $pointCuts;
        $this->locator = $locator;
    }

    public function setAspectWeaver(AspectWeaver $aspect) {
        $this->aspect = $aspect;
    }

    public function generate(Type $type, CodeWriter $writer) {
        $writer->write('function __construct($s, $m, $i, $r, $d) {');
        $writer->indent();
        $writer->writeln('$this->s = $s;');
        $writer->writeln('$this->m = $m;');
        $writer->writeln('$this->i = $i;');
        $writer->writeln('$this->r = $r;');
        $writer->write('$this->d = $d;');
        $writer->outdent();
        $writer->writeln("}");

        foreach($type->getMethods(Method::IS_PUBLIC) as $method) {
            if($method->isConstructor()) {
                continue;
            }

            if($this->pointCuts->matches($method)) {
                $this->generateMethod($method, $this->pointCuts->getAdvices($method), $writer);
            } else {
                $this->generateDelegateMethod($method, $writer);
            }
        }
    }

    public function generateDelegateMethod(Method $method, CodeWriter $writer) {
        $writer->write("function ", $method->name, "(");
        $parameters = $method->getParameters();
        if($parameters) {
            $this->generateParameter(array_shift($parameters), $writer);
            foreach($parameters as $parameter) {
                $writer->write(", ");
                $this->generateParameter($parameter, $writer);
            }
        }
        $writer->write(") {");
        $writer->indent();
            $writer->write('return $this->d->', $method->name, "(");
            $parameters = $method->getParameters();
            if($parameters) {
                $writer->write('$', array_shift($parameters)->name);
                foreach($parameters as $parameter) {
                    $writer->write(', $', $parameter->name);
                }
            }
            $writer->write(");");
        $writer->outdent();
        $writer->write("}");
    }

    public function generateMethod(Method $method, array $advices, CodeWriter $writer) {
        $writer->write("function ", $method->name, " (");
        $parameters = $method->getParameters();
        if($parameters) {
            $this->generateParameter(array_shift($parameters), $writer);
            foreach($parameters as $parameter) {
                $writer->write(", ");
                $this->generateParameter($parameter, $writer);
            }
        }
        $writer->write(") {");
        $writer->indent();
        $writer->writeln('$s = $this->s;');
        $writer->writeln('$m = $this->m;');
        $writer->writeln('$i = $this->i;');
        $writer->write("return (");
        $writer->indent();
        foreach($advices as $advice) {
            $writer->write("new DelegateInvocation(");

            $advice->accept(new FactoryCompilerVisitor($writer, $this->locator, $this->aspect));

            $writer->write(", ");
        }

        $writer->write("new TerminalInvocation(func_get_args(), ");
        $writer->literal($method->getType()->name);
        $writer->write(", ");
        $writer->literal($method->name);
        $writer->write(', $this->d, $this->r)');

        foreach($advices as $advice) {
            $writer->write(")");
        }
        $writer->outdent();
        $writer->write(")->proceed();");
        $writer->outdent();
        $writer->write("}");
    }

    public function generateParameter(Parameter $parameter, CodeWriter $writer) {
        if($parameter->getClass()) {
            $writer->write($parameter->getClass()->name, " ");
        } else if($parameter->isArray()) {
            $writer->write("array ");
        }
        $writer->write('$', $parameter->name);
        if($parameter->isDefaultValueAvailable()) {
            $writer->write(" = ");
            $writer->literal($parameter->getDefaultValue());
        }
    }
}
