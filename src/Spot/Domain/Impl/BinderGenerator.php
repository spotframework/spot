<?php
namespace Spot\Domain\Impl;

use Spot\Domain\Bind;
use Spot\Gen\CodeWriter;
use Spot\Reflect\Method;
use Spot\Reflect\Parameter;
use Spot\Reflect\Reflection;
use Spot\Reflect\Type;

class BinderGenerator {
    private $reflection;

    public function __construct(Reflection $reflection) {
        $this->reflection = $reflection;
    }

    public function generateNewInstance($domainName, CodeWriter $writer) {
        $type = $this->reflection->get($domainName);
        $ctor = $type->getConstructor();

        $writer->write('return self::bind(');
        $writer->indent();
        $writer->writeln('$d, ');
        $writer->write('new ', $domainName, "(");
        if($ctor && $ctor->getParameters()) {
            $parameters = $ctor->getParameters();
            if($parameters) {
                $writer->indent();
                $this->writeParameter(array_shift($parameters), $writer);
                foreach($parameters as $parameter) {
                    $writer->writeln(", ");
                    $this->writeParameter($parameter, $writer);
                }
                $writer->outdent();
            }
        }
        $writer->writeln('), ');
        $writer->write('$b');
        $writer->outdent();
        $writer->write(');');
    }

    public function generateBind($domainName, CodeWriter $writer) {
        $type = $this->reflection->get($domainName);
        foreach($type->getMethods(Method::IS_PUBLIC) as $method) {
            if($method->isAnnotatedWith("Spot\\Domain\\Bind")) {
                $method->getAnnotation("Spot\\Domain\\Bind")->multi
                    ? $this->writeMultiMethod($method, $writer)
                    : $this->writeMethod($method, $writer);
            }
        }

        $writer->write('return $i;');
    }

    public function writeMethod(Method $method, CodeWriter $writer) {
        if($method->getNumberOfRequiredParameters() !== 1) {
            throw new \LogicException("@Bind only for setter method");
        }

        $name = null;
        if(stripos($method->name, "set") === 0) {
            $name = lcfirst(substr($method->name, 3));
        }
        $name = $method->getAnnotation("Spot\\Domain\\Bind")->value ?: $name;
        if(empty($name)) {
            throw new \LogicException("Invalid binding name in {$method->class}::{$method->name}()");
        }

        $writer->write('$i->', $method->name, '(');
        ($class = $method->getParameters()[0]->getClass())
            ? $this->writeTypedBinding($class, $name, $writer)
            : $this->writeBinding($name, $writer);
        $writer->writeln(');');
    }

    public function writeMultiMethod(Method $method, CodeWriter $writer) {
        $name = null;
        if(stripos($method->name, "add") === 0) {
            $name = lcfirst(substr($method->name, 3));
        }
        $name = $method->getAnnotation("Spot\\Domain\\Bind")->value ?: $name;
        if(empty($name)) {
            throw new \LogicException("Invalid binding name in {$method->class}::{$method->name}()");
        }

        $writer->write('foreach((array)');
        $this->writeBinding($name, $writer);
        $writer->write(' as $v) {');
        $writer->indent();
        $writer->write('$i->', $method->name, '(');

        $class = $method->getParameters()[0]->getClass();
        if($class) {
            $writer->write('is_array($v) ? $d->newInstance(');
            $writer->literal($class->name);
            $writer->write(', $v) : $d->find(');
            $writer->literal($class->name);
            $writer->write(', $v)');
        } else {
            $writer->write('$v');
        }

        $writer->write(');');
        $writer->outdent();
        $writer->writeln('}');
    }

    public function writeParameter(Parameter $parameter, CodeWriter $writer) {
        $bind = $parameter->getAnnotation("Spot\\Domain\\Bind") ?: new Bind();
        $name = $bind->value ?: $parameter->name;

        ($class = $parameter->getClass())
            ? $this->writeTypedBinding($class, $name, $writer)
            : $this->writeBinding($name, $writer);
    }

    public function writeBinding($name, CodeWriter $writer) {
        $writer->write('$b[');
        $writer->literal($name);
        $writer->write(']');
    }

    public function writeTypedBinding(Type $class, $name, CodeWriter $writer) {
        $writer->write('is_array($b[');
        $writer->literal($name);
        $writer->write(']) ? $d->newInstance(');
        $writer->literal($class->name);
        $writer->write(', $b[');
        $writer->literal($name);
        $writer->write(']) : $d->find(');
        $writer->literal($class->name);
        $writer->write(', $b[');
        $writer->literal($name);
        $writer->write('])');
    }
}
