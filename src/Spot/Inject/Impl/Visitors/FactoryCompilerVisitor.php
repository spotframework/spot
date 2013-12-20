<?php
namespace Spot\Inject\Impl\Visitors;

use Spot\Gen\CodeWriter;
use Spot\Inject\Bindings\CollectionBinding;
use Spot\Inject\Bindings\ConfigCollectionBinding;
use Spot\Inject\Bindings\ConfigItemBinding;
use Spot\Inject\Bindings\ConstantNameBinding;
use Spot\Inject\Bindings\ConstantValueBinding;
use Spot\Inject\Bindings\InjectorBinding;
use Spot\Inject\Bindings\LateBinding;
use Spot\Inject\Bindings\OptionalBinding;
use Spot\Inject\Bindings\ProviderMethodBinding;
use Spot\Inject\Bindings\SingletonBinding;
use Spot\Inject\Bindings\InlineBinding;
use Spot\Inject\Impl\Aspect\AspectWeaver;
use Spot\Inject\Impl\BindingLocator;

class FactoryCompilerVisitor extends AbstractVisitor {
    private $writer,
            $locator,
            $aspect;

    public function __construct(
            CodeWriter $writer,
            BindingLocator $locator,
            AspectWeaver $aspect) {
        $this->writer = $writer;
        $this->locator = $locator;
        $this->aspect = $aspect;
    }

    public function visitConstantValue(ConstantValueBinding $constantValue) {
        $this->writer->literal($constantValue->getValue());
    }

    public function visitConstantName(ConstantNameBinding $constantName) {
        $this->writer->write($constantName->getName());
    }

    public function visitConfigItem(ConfigItemBinding $configItem) {
        $dependencies = $configItem->getDependencies();
        $last = array_pop($dependencies);
        foreach($dependencies as $dependency) {
            $dependency->accept($this);
            $this->writer->write(".");
        }
        $last->accept($this);
    }

    public function visitConfigCollection(ConfigCollectionBinding $configCollection) {
        $this->writer->write("[");
        $this->writer->indent();
        foreach($configCollection->getElements() as $name => $element) {
            $this->writer->literal($name);
            $this->writer->write(" => ");
            $element->accept($this);
            $this->writer->writeln(", ");
        }
        $this->writer->outdent();
        $this->writer->write("]");
    }

    public function visitInline(InlineBinding $inline) {
        if($this->aspect->check($inline->getType())) {
            $this->writer->write('$a->get(');
            $this->writer->literal($inline->getType()->name);
            $this->writer->write(', ');
        }

        $this->writer->write("new ", $inline->getType()->name, "(");
        $dependencies = $inline->getDependencies();
        if($dependencies) {
            $this->writer->indent();
            array_shift($dependencies)->accept($this);
            foreach($dependencies as $dependency) {
                $this->writer->writeln(", ");
                $dependency->accept($this);
            }

            $this->writer->outdent();
        }
        $this->writer->write(")");

        if($this->aspect->check($inline->getType())) {
            $this->writer->write(', $i)');
        }
    }

    public function visitProviderMethod(ProviderMethodBinding $providerMethod) {
        $method = $providerMethod->getMethod();
        $method->isStatic()
            ? $this->writer->write($method->getType()->name, "::", $method->name)
            : $this->writer->write('$m[', $providerMethod->getIndex(),']->', $method->name);

        $this->writer->write("(");
        $dependencies = $providerMethod->getDependencies();
        if($dependencies) {
            $this->writer->indent();
            array_shift($dependencies)->accept($this);
            foreach($dependencies as $dependency) {
                $this->writer->writeln(", ");
                $dependency->accept($this);
            }
            $this->writer->outdent();
        }
        $this->writer->write(")");
    }

    public function visitSingleton(SingletonBinding $singleton) {
        $this->writer->write('$s[', $singleton->getIndex(), '] ?: $s[', $singleton->getIndex(), '] = ');

        parent::visitSingleton($singleton);
    }

    public function visitCollection(CollectionBinding $collection) {
        $this->writer->write("[");
        $elements = $collection->getElements();
        if($elements) {
            $this->writer->indent();
            foreach($elements as $element) {
                $element->accept($this);
                $this->writer->writeln(", ");
            }
            $this->writer->outdent();
        }
        $this->writer->write("]");
    }

    public function visitLate(LateBinding $late) {
        $this->locator->get($late->getKey())->accept($this);
    }

    public function visitInjector(InjectorBinding $injector) {
        $this->writer->write('$i');
    }

    public function visitOptional(OptionalBinding $optional) {
        $resolution = $this->locator->get($optional->getKey());
        if(empty($resolution)) {
            $optional->getDefault()->accept($this);
        } else {
            $resolution->accept($this);
        }
    }
}
