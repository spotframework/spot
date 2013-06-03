<?php
namespace Spot\Inject\Impl;

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

interface BindingVisitor {
    function visitInjector(InjectorBinding $binding);
    
    function visitInline(InlineBinding $binding);
    
    function visitInstance(InstanceBinding $binding);
    
    function visitLazy(LazyBinding $binding);
    
    function visitProviderMethod(ProviderMethodBinding $binding);
    
    function visitSingleton(SingletonBinding $binding);
    
    function visitUnresolved(UnresolvedBinding $binding);
    
    function visitCollection(CollectionBinding $binding);
    
    function visitConstant(ConstantBinding $binding);
    
    function visitOptional(OptionalBinding $binding);
}