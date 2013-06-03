<?php
namespace Spot\Inject\Impl\Binding;

use Spot\Inject\Key;
use Spot\Inject\Impl\Binding;
use Spot\Inject\Impl\BindingVisitor;

class LazyBinding implements Binding {
    private $key,
            $delegate;
    
    public function __construct(Key $key, Binding $delegate) {
        $this->key = $key;
        $this->delegate = $delegate;
    }
    
    public function getKey() {
        return $this->key;
    }
    
    public function delegateAccept(BindingVisitor $visitor) {
        $this->delegate->accept($visitor);
    }
    
    public function accept(BindingVisitor $visitor) {
        $visitor->visitLazy($this);
    }    
}