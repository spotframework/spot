<?php
namespace Spot\Inject\Impl;

use Spot\Inject\Binding;
use Spot\Inject\BindingVisitor;
use Spot\Inject\Key;
use Spot\Inject\Bindings\InjectorBinding;

class Bindings implements \IteratorAggregate {
    private $bindings = [];

    public function __construct() {
        $this->put(new InjectorBinding());
    }

    public function put(Binding $binding) {
        $this->bindings[$binding->getKey()->hash()] = $binding;
    }

    /**
     * @param Key $key
     * @return Binding
     */
    public function get(Key $key) {
        if(isset($this->bindings[$key->hash()])) {
            return $this->bindings[$key->hash()];
        }
    }

    public function accept(BindingVisitor $visitor) {
        foreach($this->bindings as $binding) {
            $binding->accept($visitor);
        }
    }

    public function getIterator() {
        return new \ArrayIterator(array_values($this->bindings));
    }
}
