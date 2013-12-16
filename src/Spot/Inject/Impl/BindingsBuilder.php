<?php
namespace Spot\Inject\Impl;

use Spot\Inject\Impl\Visitors\SingletonMarkerVisitor;
use Spot\Inject\Named;

class BindingsBuilder {
    private $builded = false,
            $bindings,
            $modules,
            $binder,
            $visitors,
            $singletons,
            $marker;

    public function __construct(
            Bindings $bindings,
            Modules $modules,
            Binder $binder,
            array $visitors,
            Singletons $singletons,
            SingletonMarkerVisitor $marker) {
        $this->bindings = $bindings;
        $this->modules = $modules;
        $this->binder = $binder;
        $this->visitors = $visitors;
        $this->singletons = $singletons;
        $this->marker = $marker;
    }

    public function build() {
        if($this->builded) {
            return;
        }

        $this->binder->bind($this->modules);
        foreach($this->visitors as $visitor) {
            $this->bindings->accept($visitor);
        }

        $this->singletons->setSize($this->marker->getCount());

        $this->builded = true;
    }
}
