<?php
namespace Spot\Inject\Impl\Visitors;

use Spot\Inject\Binding;
use Spot\Inject\BindingVisitor;
use Spot\Inject\Bindings\CollectionBinding;
use Spot\Inject\Bindings\ConfigBinding;
use Spot\Inject\Bindings\ConfigCollectionBinding;
use Spot\Inject\Bindings\ConfigItemBinding;
use Spot\Inject\Bindings\ConstantBinding;
use Spot\Inject\Bindings\ConstantNameBinding;
use Spot\Inject\Bindings\ConstantValueBinding;
use Spot\Inject\Bindings\InlineBinding;
use Spot\Inject\Bindings\LateBinding;
use Spot\Inject\Bindings\ModuleBinding;
use Spot\Inject\Bindings\OptionalBinding;
use Spot\Inject\Bindings\ProviderMethodBinding;
use Spot\Inject\Bindings\SingletonBinding;
use Spot\Inject\Bindings\InjectorBinding;
use Spot\Inject\ConfigurationException;
use Spot\Inject\Bindings\LazyBinding;

class AbstractVisitor implements BindingVisitor {
    function visit(Binding $binding) {
        $binding instanceof CollectionBinding &&
            $this->visitCollection($binding);

        $binding instanceof ConfigBinding &&
            $this->visitConfig($binding);

        $binding instanceof ConfigCollectionBinding &&
            $this->visitConfigCollection($binding);

        $binding instanceof ConfigItemBinding &&
            $this->visitConfigItem($binding);

        $binding instanceof ConstantBinding &&
            $this->visitConstant($binding);

        $binding instanceof ConstantNameBinding &&
            $this->visitConstantName($binding);

        $binding instanceof ConstantValueBinding &&
            $this->visitConstantValue($binding);

        $binding instanceof InlineBinding &&
            $this->visitInline($binding);

        $binding instanceof LateBinding &&
            $this->visitLate($binding);

        $binding instanceof ModuleBinding &&
            $this->visitModule($binding);

        $binding instanceof OptionalBinding &&
            $this->visitOptional($binding);

        $binding instanceof ProviderMethodBinding &&
            $this->visitProviderMethod($binding);

        $binding instanceof SingletonBinding &&
            $this->visitSingleton($binding);

        $binding instanceof InjectorBinding &&
            $this->visitInjector($binding);

        $binding instanceof LazyBinding &&
            $this->visitLazy($binding);
    }

    public function visitCollection(CollectionBinding $collection) {
        foreach($collection->getElements() as $element) {
            $element->accept($this);
        }
    }

    public function visitConfig(ConfigBinding $config) {}

    public function visitConfigCollection(ConfigCollectionBinding $configCollection) {
        foreach($configCollection->getElements() as $element) {
            $element->accept($this);
        }
    }

    public function visitConfigItem(ConfigItemBinding $configItem) {
        foreach($configItem->getDependencies() as $dependency) {
            $dependency->accept($this);
        }
    }

    public function visitConstant(ConstantBinding $constant) {}

    public function visitConstantName(ConstantNameBinding $constantName) {}

    public function visitConstantValue(ConstantValueBinding $constantValue) {}

    public function visitInline(InlineBinding $inline) {
        foreach($inline->getDependencies() as $dependency) {
            $dependency->accept($this);
        }
    }

    public function visitLate(LateBinding $late) {}

    public function visitModule(ModuleBinding $module) {}

    public function visitOptional(OptionalBinding $optional) {}

    public function visitProviderMethod(ProviderMethodBinding $providerMethod) {
        foreach($providerMethod->getDependencies() as $dependency) {
            $dependency->accept($this);
        }
    }

    public function visitSingleton(SingletonBinding $singleton) {
        $singleton->getDelegate()->accept($this);
    }

    public function visitInjector(InjectorBinding $injector) {}

    public function visitLazy(LazyBinding $lazy) {}
}
