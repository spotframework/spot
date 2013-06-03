<?php
namespace Spot\Inject\Impl\Lazy;

use Spot\Reflect\Type;
use Spot\Reflect\Method;
use Spot\Reflect\Parameter;
use Spot\Code\Impl\CodeWriterImpl;

class LazyProxyGenerator {
    private $writer;
    
    public function generate(Type $type, $className) {
        $this->writer = new CodeWriterImpl();
        
        $this->writer->writeln('use Spot\Inject\Key;');
        $this->writer->writeln('use Spot\Inject\Injector;');
        
        $this->writer->newLine();
        $this->writer->write('class ');
        $this->writer->write($className);
        
        $type->isInterface()
            ? $this->writer->write(' implements ')
            : $this->writer->write(' extends ');
        
        $this->writer->write('\\');
        $this->writer->write($type->name);
        $this->writer->writeln(' {');
        
        $this->writer->writeln('    public $i, $k, $d;

    function __construct(Injector $i, Key $k) {
        $this->i = $i;
        $this->k = $k;
    }
    
    function __d() {
        return $this->d ?: $this->d = $this->i->get($this->k);
    }');
        $this->writer->indent();
        
        foreach($type->getMethods(Method::IS_PUBLIC) as $method) {
            if($method->isConstructor() || $method->isStatic()) continue;
            
            $this->writeMethod($method);
            $this->writer->newLine();
            $this->writer->newLine();
        }
        
        $this->writer->outdent();
        $this->writer->write('}');
        
        return (string)$this->writer;
    }
    
    public function writeMethod(Method $method) {
        $this->writer->write('function ');
        $this->writer->write($method->name);
        $this->writer->write('(');
        $this->writeParameters($method->getParameters());
        $this->writer->write(') {');
        $this->writer->indent();
        
        $this->writer->write('return $this->__d()->');
        $this->writer->write($method->name);
        $this->writer->write('(');
        $parameters = $method->getParameters();
        if(!empty($parameters)) {
            $this->writer->write('$');
            $this->writer->write(array_shift($parameters)->name);
            foreach($parameters as $parameter) {
                $this->writer->write(', $');
                $this->writer->write($parameter->name);
            }
        }
        
        $this->writer->write(');');
        
        $this->writer->outdent();
        $this->writer->write('}');
    }
    
    public function writeParameters(array $parameters) {
        if(empty($parameters)) return;
        
        $this->writeParameter(array_shift($parameters));
        foreach($parameters as $parameter) {
            $this->writer->write(', ');
            $this->writeParameter($parameter);
        }
    }
    
    public function writeParameter(Parameter $parameter) {
        if(($class = $parameter->getClass())) {
            $this->writer->write('\\');
            $this->writer->write($class->name);
            $this->writer->write(' ');
        } else if($parameter->isArray()) {
            $this->writer->write('array ');
        }
        $this->writer->write('$');
        $this->writer->write($parameter->name);
        if($parameter->isDefaultValueAvailable()) {
            $this->writer->write(' = ');
            $this->writer->writeValue($parameter->getDefaultValue());
        }
    }
}