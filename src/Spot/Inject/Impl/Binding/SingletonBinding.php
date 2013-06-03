<?php
namespace Spot\Inject\Impl\Binding;

use Spot\Inject\Impl\Binding;
use Spot\Inject\Impl\BindingVisitor;

class SingletonBinding implements Binding {
    private $index,
            $delegate;
    
    public function __construct(Binding $delegate) {
        $this->delegate = $delegate;
    }

    public function getKey() {
        return $this->delegate->getKey();
    }
    
    public function getDelegate() {
        return $this->delegate;
    }
    
    public function delegateAccept(BindingVisitor $visitor) {
        $this->delegate->accept($visitor);
    }
    
    public function setIndex($index) {
        $this->index = $index;
    }
    
    public function getIndex() {
        return $this->index;
    }
    
    public function accept(BindingVisitor $visitor) {
        $visitor->visitSingleton($this);
    }    
}