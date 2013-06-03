<?php
namespace Spot\Inject\Impl\Binding;

use Spot\Inject\Key;
use Spot\Inject\Impl\Binding;
use Spot\Inject\Impl\BindingVisitor;
use Spot\Reflect\Parameter;

class UnresolvedBinding implements Binding {
    private $key,
            $parameter,
            $delegate;
    
    public function __construct(Key $key, Parameter $parameter) {
        $this->key = $key;
        $this->parameter = $parameter;
    }
    
    public function getKey() {
        return $this->key;
    }
    
    public function getParameter() {
        return $this->parameter;
    }
    
    public function getDelegate() {
        return $this->delegate;
    }
    
    public function setDelegate(Binding $binding) {
        $this->delegate = $binding;
    }
    
    public function isResolved() {
        return (bool)$this->delegate;
    }
    
    public function delegateAccept(BindingVisitor $visitor) {
        $this->delegate->accept($visitor);
    }
    
    public function accept(BindingVisitor $visitor) {
        $visitor->visitUnresolved($this);
    }
    
    static public function create(Parameter $parameter) {
        return new self(Key::ofParameter($parameter), $parameter);
    }
}