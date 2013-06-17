<?php
namespace Spot\Domain\Impl;

use Spot\Reflect\Type;
use Spot\Reflect\Method;
use Spot\Reflect\Parameter;
use Spot\Domain\Bind\Bind;
use Spot\Domain\Impl\Binding\VarBinding;
use Spot\Domain\Impl\Binding\ValueBinding;
use Spot\Domain\Impl\Binding\TypedBinding;
use Spot\Domain\Impl\Binding\MultiBinding;
use Spot\Domain\Impl\Binding\MethodBinding;
use Spot\Domain\Impl\Binding\InstantiationBinding;
use Spot\Code\Impl\CodeWriterImpl;

class BinderGenerator {
    public function generate(Type $type) {
        $binderName = 'Binder__'.md5($type->name);
        $writer = new CodeWriterImpl();
        $writer->writeln('use Spot\Domain\DomainManager;');
        $writer->newLine();
        $writer->writeln('/**');
        $writer->write(' * Bind \\');
        $writer->writeln($type->name);
        $writer->writeln(' */');
        $writer->write('class ');
        $writer->write($binderName);
        $writer->write(' {');
        $writer->indent();
        
            $writer->write('function __construct(DomainManager $d) {');
            $writer->indent();
            $writer->write('$this->d = $d;');
            $writer->outdent();
            $writer->writeln('}');

            $writer->write('function newInstance(array $b) {');
            $writer->indent();

            $writer->write('$i = ');
            $this->bindConstructor($type)->compile($writer);
            
            $writer->newLine();
            $writer->newLine();
            $writer->write('return $this->bind($i, $b);');
            
            $writer->outdent();
            $writer->writeln('}');
            
            $writer->write('function bind($i, array $b) {');
            $writer->indent();
                
            $methods = $this->bindMethods($type);
            if(!empty($methods)) {
                array_shift($methods)->compile($writer);
                foreach($methods as $method) {
                    $method->compile($writer);
                }
            }
            
            $writer->write('return $i;');
            $writer->outdent();
            $writer->write('}');
        
        $writer->outdent();
        $writer->write('}');

        return $writer->getCode();
    }
    
    public function bindConstructor(Type $type) {
        $parameters = [];
        foreach(($ctor = $type->getConstructor()) ? $ctor->getParameters() : [] as $parameter) {
            $parameters[] = $this->bindConstructorParameter($parameter);
        }
        
        return new InstantiationBinding($type->name, $parameters);
    }
    
    public function bindMethods(Type $type) {
        $methods = [];
        foreach($type->getMethods() as $method) {
            if(!$method->isAnnotatedWith('Spot\Domain\Bind\Bind')) {
                continue;
            }
            
            $methods[] = $this->bindMethod($method);
        }
        
        return $methods;
    }
    
    public function bindMethod(Method $method) {        
        $bind = $method->getAnnotation('Spot\Domain\Bind\Bind') ?: new Bind();
        
        return $bind->multiple
                ? $this->bindMultiple($bind, $method)
                : $this->bindSetter($bind, $method);
    }
    
    public function bindMultiple(Bind $bind, Method $method) {
        static $prefix = 'add';
        if(empty($bind->value)) {
            if(strpos($method->name, $prefix) === 0) {
                $bind->value = lcfirst(substr($method->name, strlen($prefix)));
            } else {
                throw new \LogicException('add prefix');
            }
        }
        
        $bindings = new ValueBinding($bind->value);
        $class = $method->getParameters()[0]->getClass();
        
        return new MultiBinding(
            $bindings, 
            $class ? new TypedBinding(new VarBinding('v'), $class->name) : new VarBinding('v'), 
            $method->name
        );
    }
    
    public function bindSetter(Bind $bind, Method $method) {
        static $prefix = 'set';
        if(empty($bind->value)) {
            if (strpos($method->name, $prefix) === 0) {
                $bind->value = lcfirst(substr($method->name, strlen($prefix)));
            } else {   
                throw new \LogicException('set prefix');
            }
        }
        
        $value = new ValueBinding($bind->value);
        $class = $method->getParameters()[0]->getClass();
        
        return new MethodBinding(
            $value, 
            $class ? new TypedBinding($value, $class->name) : $value,
            $method->name
        );
    }
    
    public function bindConstructorParameter(Parameter $parameter) {
        $bind = $parameter->getAnnotation('Spot\Domain\Bind\Bind') ?: new Bind();
        $bind->value = $bind->value ?: $parameter->name;
        
        return ($class = $parameter->getClass()) 
                ? new TypedBinding(new ValueBinding($bind->value), $class->name)
                : new ValueBinding($bind->value);
    }
}