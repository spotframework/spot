<?php
namespace Spot\Inject\Impl\Binding;

use Spot\Inject\Impl\Binding;
use Spot\Inject\Impl\BindingVisitor;

class InjectorBinding implements Binding {
    public function getKey() {
        return Key::ofType("Spot\Inject\Injector");
    }

    public function accept(BindingVisitor $visitor) {
        $visitor->visitInjector($this);
    }    
}