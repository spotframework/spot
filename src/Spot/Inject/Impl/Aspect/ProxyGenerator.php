<?php
namespace Spot\Inject\Impl\Aspect;

use Spot\Reflect\Type;
use Spot\Reflect\Method;
use Spot\Reflect\Parameter;
use Spot\Code\CodeWriter;
use Spot\Code\Impl\CodeWriterImpl;
use Spot\Inject\Impl\Visitor\PhpCompiler;

class ProxyGenerator {
    private $aspect;
    
    public function setAspect(AspectWeaver $aspect) {
        $this->aspect = $aspect;
    }
    
    public function generate(Type $type, $proxyName, array $advices) {
        $writer = new CodeWriterImpl();
        
        $writer->writeln('use Spot\Inject\Key;');
        $writer->writeln('use Spot\Reflect\Reflection;');
        $writer->writeln('use Spot\Inject\Impl\Modules;');
        $writer->writeln('use Spot\Inject\Impl\InjectorImpl;');
        $writer->writeln('use Spot\Inject\Impl\Aspect\DelegateInvocation;');
        $writer->writeln('use Spot\Inject\Impl\Aspect\TerminalInvocation;');
        $writer->newLine();
        
        $writer->write('class ');
        $writer->write($proxyName);
        $type->isInterface()
                ? $writer->write(' implements \\')
                : $writer->write(' extends \\');
        
        $writer->write($type->name);
        $writer->write(' {');
        $writer->indent();
        
        $writer->writeln('public $r, $m, $i, $d;');
        
        $writer->write('function __construct(Reflection $reflection, InjectorImpl $injector, Modules $modules, \\');
        $writer->write($type->name);
        $writer->write(' $delegate');
        $writer->write(') {');
        $writer->indent();
        $writer->writeln('$this->r = $reflection;');
        $writer->writeln('$this->m = $modules;');
        $writer->writeln('$this->i = $injector;');
        $writer->write('$this->d = $delegate;');
        $writer->outdent();
        $writer->writeln('}');
        $writer->newLine();
        
        foreach($type->getMethods(Method::IS_PUBLIC) as $method) {
            if(isset($advices[$method->name])) {
                $this->generateMethod($method, $advices[$method->name], $writer);
            } else if(!$method->isConstructor()) {
                $writer->write("function {$method->name} (");
                
                $parameters = $method->getParameters();
                if($parameters) {
                    $this->generateParameter(array_shift($parameters), $writer);
                    foreach($parameters as $parameter) {
                        $writer->write(', ');
                        $this->generateParameter($parameter, $writer);
                    }
                }
                
                $writer->write(') {');
                $writer->indent();
                $writer->write('return $this->d->');
                $writer->write($method->name);
                $writer->write('(');
                
                $parameters = $method->getParameters();
                if($parameters) {
                    $writer->write('$');
                    $writer->write(array_shift($parameters)->name);
                    foreach($parameters as $parameter) {
                        $writer->write(', $');
                        $writer->write($parameter->name);
                    }
                }
                
                $writer->write(');');
                $writer->outdent();
                $writer->write("}");
                $writer->newLine();
                $writer->newLine();
            }
        }
        
        $writer->outdent();
        $writer->write('}');
        
        return $writer->getCode();
    }
    
    public function generateMethod(Method $method, array $advices, CodeWriter $writer) {
        $writer->write('function ');
        $writer->write($method->name);
        $writer->write('(');
        $parameters = $method->getParameters();
        if($parameters) {
            $this->generateParameter(array_shift($parameters), $writer);
            
            foreach($parameters as $parameter) {
                $writer->write(', ');
                
                $this->generateParameter($parameter, $writer);
            }
        }
        
        $writer->write(') {');
        $writer->indent();
        
        $writer->writeln('$i = $this->i;');
        $writer->writeln('$m = $this->m;');
        $writer->writeln('$s = $i->getSingletonPool();');
        
        $writer->write('return (');
        for($i = count($advices); $i--;) {
            $writer->indent();
            $writer->write('new DelegateInvocation(');
        }
        
        $writer->indent();
        $writer->write('new TerminalInvocation($this->d, $this->r->getMethod($this->r->getType(');
        $writer->writeValue($method->getType()->name);
        $writer->write('), __FUNCTION__), func_get_args())');
        
        foreach(array_reverse($advices) as $advice) {
            $writer->writeln(',');
            $advice->accept(new PhpCompiler($writer, $this->aspect));
            $writer->outdent();
            $writer->write(')');
        }
        
        $writer->outdent();
        $writer->write(')->proceed();');
        
        $writer->outdent();
        $writer->write('}');
    }
    
    public function generateParameter(Parameter $parameter, CodeWriter $writer) {
        if(($class = $parameter->getClass())) {
            $writer->write('\\');
            $writer->write($class->name);
            $writer->write(' ');
        } else if($parameter->isArray()) {
            $writer->write('array ');
        }
        $writer->write('$');
        $writer->write($parameter->name);
        if($parameter->isDefaultValueAvailable()) {
            $writer->write(' = ');
            $writer->writeValue($parameter->getDefaultValue());
        }
    }
}
