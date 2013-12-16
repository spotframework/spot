<?php
namespace Spot\Inject\Impl;

use Spot\Inject\Impl\Binders\JustInTimeBinder;
use Spot\Inject\Key;
use Spot\Reflect\Reflection;

class BindingLocator {
    private $bindings,
            $jitBinder,
            $reflection;

    public function __construct(
            Bindings $bindings,
            JustInTimeBinder $jitBinder,
            Reflection $reflection) {
        $this->bindings = $bindings;
        $this->jitBinder = $jitBinder;
        $this->reflection = $reflection;
    }

    public function get(Key $key) {
        $binding = $this->bindings->get($key);
        if(empty($binding) && $key->isType()) {
            $this->jitBinder->bind($this->reflection->get($key->getType()));

            $binding = $this->bindings->get($key);
        }

        return $binding;
    }
}
