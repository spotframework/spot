<?php
namespace Spot\Inject\Impl\Visitor;

use Spot\Code\CodeWriter;
use Spot\Inject\Key;
use Spot\Inject\ElementKey;
use Spot\Inject\Impl\Aspect\AspectWeaver;
use Spot\Inject\Impl\BindingVisitor;
use Spot\Inject\Impl\Binding\InjectorBinding;
use Spot\Inject\Impl\Binding\InlineBinding;
use Spot\Inject\Impl\Binding\InstanceBinding;
use Spot\Inject\Impl\Binding\LazyBinding;
use Spot\Inject\Impl\Binding\ProviderMethodBinding;
use Spot\Inject\Impl\Binding\SingletonBinding;
use Spot\Inject\Impl\Binding\UnresolvedBinding;
use Spot\Inject\Impl\Binding\CollectionBinding;
use Spot\Inject\Impl\Binding\ConstantBinding;
use Spot\Inject\Impl\Binding\OptionalBinding;

class PhpCompiler implements BindingVisitor {
    private $writer,
            $aspect;
    
    public function __construct(CodeWriter $writer, AspectWeaver $aspect) {
        $this->writer = $writer;
        $this->aspect = $aspect;
    }
    
    public function writeParameters(array $parameters) {
        $this->writer->write('(');
        if(!empty($parameters)) {
            $this->writer->indent();
            array_shift($parameters)->accept($this);
            foreach($parameters as $parameter) {
                $this->writer->writeln(',');
                $parameter->accept($this);
            }
            $this->writer->outdent();
        }
        $this->writer->write(')');
    }
    
    public function visitInjector(InjectorBinding $binding) {
        $this->writer->write('$i');
    }

    public function visitInline(InlineBinding $binding) {        
        $key = Key::ofType($binding->getClass());
        if($this->aspect->isInterceptedNamed($key->getTypeName())) {
            $this->writer->write('$i->getWovenProxy(Key::ofType(');
            $this->writer->writeValue($key->getTypeName());
            $this->writer->write('), ');
            $this->writer->write('new \\');
            $this->writer->write($binding->getClass());
            $this->writeParameters($binding->getParameters());
            $this->writer->write(')');
        } else {
            $this->writer->write('new \\');
            $this->writer->write($binding->getClass());
            $this->writeParameters($binding->getParameters());
        }
    }
    
    public function visitInstance(InstanceBinding $binding) {
        $this->writer->write('/* ');
        $this->writer->write($binding->getKey());
        $this->writer->write(' */');
        $this->writer->write('$s[');
        $this->writer->writeValue($binding->getIndex());
        $this->writer->write(']');
    }

    public function visitLazy(LazyBinding $binding) {
        $this->writer->write('$i->getLazy(Key::ofType(');
        $key = $binding->getKey();
        
        $this->writer->writeValue($key->getTypeName());
        
        $qualifier = $key->getQualifier();
        if($qualifier) {
            $this->writer->write(', unserialize(');
            $this->writer->writeValue(serialize($qualifier));
            $this->writer->write(')');
        }
        
        $this->writer->write('))');
    }

    public function visitProviderMethod(ProviderMethodBinding $binding) {
        if(!$binding->getKey() instanceof ElementKey) {
            $this->writer->write('/* ');
            $this->writer->write($binding->getKey());
            $this->writer->write(' */');
        }
        
        $this->writer->write('\\');
        $this->writer->write($binding->getModule());
        $this->writer->write('::');
        $this->writer->write($binding->getProvider());
        $this->writeParameters($binding->getParameters());
    }

    public function visitSingleton(SingletonBinding $binding) {
        $this->writer->write('$s[');
        $this->writer->writeValue($binding->getIndex());
        $this->writer->write('] ?: $s[');
        $this->writer->writeValue($binding->getIndex());
        $this->writer->write('] = ');
        $binding->delegateAccept($this);
    }

    public function visitUnresolved(UnresolvedBinding $binding) {
        $binding->delegateAccept($this);
    }

    public function visitCollection(CollectionBinding $binding) {
        $this->writer->write('/* ');
        $this->writer->write($binding->getKey());
        $this->writer->write(' */');
        
        $elements = $binding->getElements();
        if(empty($elements)) {
            $this->write('[]');
        } else if(count($elements) === 1) {
            $this->writer->write('[');
            current($elements)->accept($this);
            $this->writer->write(']');
        } else {
            $this->writer->write('[');
            $this->writer->indent();
            foreach($elements as $element) {
                $element->accept($this);
                $this->writer->writeln(', ');
            }
            $this->writer->outdent();
            $this->writer->write(']');
        }
    }

    public function visitConstant(ConstantBinding $binding) {
        $this->writer->write('/* ');
        $this->writer->write($binding->getKey());
        $this->writer->write(' */');
        $this->writer->writeValue($binding->getValue());
    }
    
    public function visitOptional(OptionalBinding $binding) {
        $binding->isResolved()
            ? $binding->delegateAccept($this)
            : $this->writer->writeValue($binding->getDefaultValue());
    }
}