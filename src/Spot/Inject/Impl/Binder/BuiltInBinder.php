<?php
namespace Spot\Inject\Impl\Binder;

use Spot\Inject\Key;
use Spot\Inject\Impl\Bindings;
use Spot\Inject\Impl\SingletonPool;
use Spot\Inject\Impl\Binding\InstanceBinding;

class BuiltInBinder {
    private $bindings,
            $singletons;
    
    public function __construct(
            Bindings $bindings,
            SingletonPool $singletons) {
        $this->bindings = $bindings;
        $this->singletons = $singletons;
    }
    
    public function bind(Key $key, $instance) {
        $index = $this->singletons->getSize();
        $this->singletons->setSize($index + 1);
        
        $this->singletons[$index] = $instance;
        $binding = new InstanceBinding($key, $index);
        $this->bindings->put($key, $binding);
    }
}