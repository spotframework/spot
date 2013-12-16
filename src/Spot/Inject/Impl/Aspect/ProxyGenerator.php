<?php
namespace Spot\Inject\Impl\Aspect;

use Spot\Gen\CodeWriter;
use Spot\Inject\Impl\BindingLocator;
use Spot\Inject\Impl\Visitors\FactoryCompilerVisitor;
use Spot\Reflect\Method;
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
        $writer->write('function __construct($r, $d, $s) {');
        $writer->indent();
        $writer->writeln('$this->r = $r;');
        $writer->writeln('$this->d = $d;');
        $writer->write('$this->s = $s;');
        $writer->outdent();
        $writer->writeln("}");

        foreach($type->getMethods(Method::IS_PUBLIC) as $method) {
            if(!$this->pointCuts->matches($method)) {
                continue;
            }

            $this->generateMethod($method, $this->pointCuts->getAdvices($method), $writer);
        }
    }

    public function generateMethod(Method $method, array $advices, CodeWriter $writer) {
        $writer->write("function ", $method->name, " (");
        $parameters = $method->getParameters();
        if($parameters) {
            $parameter = array_shift($parameters);
            if($parameter->getClass()) {
                $writer->write($parameter->getClass()->name, " ");
            } else if($parameter->isArray()) {
                $writer->write("array ");
            }
            $writer->write('$', $parameter->name);
            foreach($parameters as $parameter) {
                if($parameter->getClass()) {
                    $writer->write($parameter->getClass()->name, " ");
                } else if($parameter->isArray()) {
                    $writer->write("array ");
                }
                $writer->write('$s', $parameter->name);
            }
        }
        $writer->write(") {");
        $writer->indent();
        $writer->writeln('$s = $this->s;');
        $writer->write("return (");
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

        $writer->write(")->proceed();");
        $writer->outdent();
        $writer->write("}");
    }
}
