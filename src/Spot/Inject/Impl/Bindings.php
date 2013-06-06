<?php
namespace Spot\Inject\Impl;

use ArrayIterator;
use IteratorAggregate;
use Spot\Inject\Key;
use Spot\Inject\Impl\Binding\InjectorBinding;

class Bindings implements IteratorAggregate {
    private $bindings = [];
    
    public function __construct() {
        $this->put(Key::ofType("Spot\Inject\Injector"), new InjectorBinding());
    }
    
    /**
     * Store {@link \Spot\Inject\Impl\Binding}
     * 
     * @param \Spot\Inject\Key $key
     * @param \Spot\Inject\Impl\Binding $binding
     */
    public function put(Key $key, Binding $binding) {
        $this->bindings[$key->hash()] = $binding;
    }
    
    /**
     * Get {@link \Spot\Inject\Impl\Binding}
     * 
     * @param \Spot\Inject\Key $key
     * @return \Spot\Inject\Impl\Binding|null null when binding not exists
     */
    public function get(Key $key) {
        if(isset($this->bindings[$key->hash()])) {
            return $this->bindings[$key->hash()];
        }
    }
    
    public function getIterator() {
        return new ArrayIterator($this->bindings);
    }
}