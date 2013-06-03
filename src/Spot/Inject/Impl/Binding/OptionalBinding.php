<?php
namespace Spot\Inject\Impl\Binding;

use Spot\Inject\Impl\Binding;
use Spot\Inject\Impl\BindingVisitor;

class OptionalBinding implements Binding {
    private $delegate,
            $defaultValue;
    
    public function __construct(UnresolvedBinding $delegate, $defaultValue) {
        $this->delegate = $delegate;
        $this->defaultValue = $defaultValue;
    }
    
    public function getKey() {
        return $this->delegate->getKey();
    }
    
    public function isResolved() {
        return $this->delegate->isResolved();
    }
    
    public function getDefaultValue() {
        return $this->defaultValue;
    }
    
    public function delegateAccept(BindingVisitor $visitor) {
        $this->delegate->accept($visitor);
    }
    
    public function accept(BindingVisitor $visitor) {
        $visitor->visitOptional($this);
    }    
}