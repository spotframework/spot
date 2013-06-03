<?php
namespace Spot\Inject\Impl\Binder\Config;

use ArrayIterator;
use IteratorAggregate;

class Configs implements IteratorAggregate {
    private $configs = [];

    public function get($name) {
        if(isset($this->configs[$name])) {
            return $this->configs[$name];
        }
    }

    public function put($name, ConfigItem $item) {
        $this->configs[$name] = $item;
    }

    public function getIterator() {
        return new ArrayIterator($this->configs);
    }
}