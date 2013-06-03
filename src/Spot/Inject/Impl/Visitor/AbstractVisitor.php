<?php
namespace Spot\Inject\Impl\Visitor;

use Spot\Inject\Impl\Bindings;
use Spot\Inject\Impl\BindingVisitor;
use Spot\Inject\Impl\Binding\CollectionBinding;
use Spot\Inject\Impl\Binding\ConstantBinding;
use Spot\Inject\Impl\Binding\InjectorBinding;
use Spot\Inject\Impl\Binding\InlineBinding;
use Spot\Inject\Impl\Binding\InstanceBinding;
use Spot\Inject\Impl\Binding\LazyBinding;
use Spot\Inject\Impl\Binding\OptionalBinding;
use Spot\Inject\Impl\Binding\ProviderMethodBinding;
use Spot\Inject\Impl\Binding\SingletonBinding;
use Spot\Inject\Impl\Binding\UnresolvedBinding;

abstract class AbstractVisitor implements BindingVisitor {
    private $bindings;
    
    public function __construct(Bindings $bindings) {
        $this->bindings = $bindings;
    }
    
    /**
     * @return Bindings
     */
    public function getBindings() {
        return $this->bindings;
    }
    
    public function visit() {
        foreach($this->bindings as $binding) {
            $binding->accept($this);
        }
    }
    
    public function visitCollection(CollectionBinding $binding) {}

    public function visitConstant(ConstantBinding $binding) {}

    public function visitInjector(InjectorBinding $binding) {}

    public function visitInline(InlineBinding $binding) {}
    
    public function visitInstance(InstanceBinding $binding) {}

    public function visitLazy(LazyBinding $binding) {}

    public function visitOptional(OptionalBinding $binding) {}

    public function visitProviderMethod(ProviderMethodBinding $binding) {}

    public function visitSingleton(SingletonBinding $binding) {}

    public function visitUnresolved(UnresolvedBinding $binding) {}
}