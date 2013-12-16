<?php
namespace Spot\App\Web\Impl\Dispatch\Method;

use Spot\App\Web\Impl\Router\RoutePathCompiler;
use Spot\Gen\CodeWriter;
use Spot\Reflect\Method;
use Spot\Reflect\Parameter;
use Spot\Reflect\Type;

class InstanceMethodGenerator extends MethodGenerator {
    private $compiler;

    public function __construct(RoutePathCompiler $compiler) {
        $this->compiler = $compiler;
    }

    public function generate(Method $method, CodeWriter $writer) {
        if(!$this->isTypeBinded($method)) {
            parent::generate($method, $writer);

            return;
        }

        $type = $method->getType();
        $ctor = $type->getConstructor();
        $route = $type->getAnnotation("Spot\\App\\Web\\Route");
        $paths = $this->compiler->parse($route->value);

        $writer->writeln('public $rq;');
        $writer->write('function __construct($rq) {');
        $writer->indent();
            $writer->write('$this->rq = $rq;');
        $writer->outdent();
        $writer->writeln('}');

        $writer->writeln('/** @Spot\Inject\Provides("', $type->name, '") */');
        $writer->write('function provideActionController(Spot\Domain\Domain $d');
        $parameters = array_filter($ctor->getParameters(), function (Parameter $parameter) use ($paths) {
            return !in_array($parameter->name, $paths);
        });
        foreach($parameters as $parameter) {
            $writer->write(", ");
            $this->generateDependency($parameter, $writer);
        }
        $writer->write(') {');
        $writer->indent();
            $writer->writeln('$rq = $this->rq;');
            $writer->write('return new ', $type->name, '(');

            $this->generateDependencyInjection($type, $writer);

            $writer->write(');');
        $writer->outdent();
        $writer->writeln('}');

        parent::generate($method, $writer);
    }

    public function generateDependency(Parameter $parameter, CodeWriter $writer) {
        $q = $parameter->getAnnotation("Spot\\Inject\\Qualifier");
        if($q) {
            $writer->write("/** @", get_class($q));
            $writer->write("(");
            $vars = get_object_vars($q);
            if($vars) {
                $name = key($vars);
                $value = array_shift($vars);
                $writer->write($name, '=');
                $writer->literal($value);
                foreach($vars as $name => $value) {
                    $writer->write(', ', $name, ' = ');
                    $writer->literal($value);
                }
            }
            $writer->write(") */");
        }

        $class = $parameter->getClass();
        if($class) {
            $writer->write($class->name, " ");
        } else if($parameter->isArray()) {
            $writer->write("array ");
        }

        $writer->write('$_', $parameter->getPosition());
    }

    public function generateDependencyInjection(Type $type, CodeWriter $writer) {
        $ctor = $type->getConstructor();
        $route = $type->getAnnotation("Spot\\App\\Web\\Route");
        $paths = $this->compiler->parse($route->value);

        $parameters = $ctor->getParameters();
        if($parameters) {
            $this->generateDependencyInjectionParameter(array_shift($parameters), $paths, $writer);

            foreach($parameters as $parameter) {
                $writer->write(', ');
                $this->generateDependencyInjectionParameter($parameter, $paths, $writer);
            }
        }
    }

    public function generateDependencyInjectionParameter(Parameter $parameter, array $paths, CodeWriter $writer) {
        $name = $parameter->isAnnotatedWith("Spot\\App\\Web\\Param")
            ? $parameter->getAnnotation("Spot\\App\\Web\\Param")->value
            : $parameter->name;

        if(in_array($name, $paths)) {
            $this->generateParameter($parameter, $writer);
        } else {
            $writer->write('$_', $parameter->getPosition());
        }
    }

    public function isTypeBinded(Method $method) {
        $type = $method->getType();
        $route = $type->getAnnotation("Spot\\App\\Web\\Route");
        if(!$route || !$route->value) {
            return false;
        }

        $paths = $this->compiler->parse($route->value);
        $ctor = $type->getConstructor();

        return $ctor && $ctor->getParameters() && $paths;
    }

    public function generateMethodCall(Method $method, CodeWriter $writer) {
        $writer->write('$i');
        if($this->isTypeBinded($method)) {
            $writer->write('->fork([new self($rq)])');
        }
        $writer->write('->getInstance(');
        $writer->literal($method->getType()->name);
        $writer->write(')->', $method->name);
    }
}
