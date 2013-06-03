<?php
namespace Spot\Inject\Impl\Visitor;

use Spot\Inject\TypeKey;
use Spot\Reflect\Reflection;
use Spot\Inject\Impl\Bindings;
use Spot\Inject\Impl\Binder\JustInTimeBinder;
use Spot\Inject\Impl\Binding\OptionalBinding;
use Spot\Inject\Impl\Binding\SingletonBinding;
use Spot\Inject\Impl\Binding\UnresolvedBinding;
use Spot\Inject\Impl\Binding\CollectionBinding;
use Spot\Inject\Impl\Binding\ProviderMethodBinding;

class BindingResolver extends AbstractVisitor {
    private $bindings,
            $reflection,
            $binder;
    
    public function __construct(
            Bindings $bindings, 
            Reflection $reflection, 
            JustInTimeBinder $binder) {
        parent::__construct($bindings);
        $this->bindings = $bindings;
        $this->reflection = $reflection;
        $this->binder = $binder;
    }

    public function visitProviderMethod(ProviderMethodBinding $binding) {
        foreach($binding->getParameters() as $parameter) {
            $parameter->accept($this);
        }
    }

    public function visitSingleton(SingletonBinding $binding) {
        $binding->delegateAccept($this);
    }

    public function visitUnresolved(UnresolvedBinding $binding) {
        if($binding->isResolved()) return;
        
        $key = $binding->getKey();
        $resolved = $this->bindings->get($key);
        if(empty($resolved) && $key instanceof TypeKey) {
            $this->binder->bind($this->reflection->getType($key->getTypeName()));
            
            $resolved = $this->bindings->get($key);
        }
        
        if(empty($resolved)) {
            $parameter = $binding->getParameter();
            throw new \LogicException(
                'Missing binding for '.$key.
                ', required by parameter $'.$parameter->name.
                ' in '.$parameter->getDeclaringFunction()->getFileName().':'.$parameter->getDeclaringFunction()->getStartLine());
        }
        
        $binding->setDelegate($resolved);
    }

    public function visitCollection(CollectionBinding $binding) {
        foreach($binding->getElements() as $element) {
            $element->accept($this);
        }
    }

    public function visitOptional(OptionalBinding $binding) {
        try {
            $binding->delegateAccept($this);
        } catch(\LogicException $e) {
            //noop
            //unresolved binding, default value will be used
        }
    }
}