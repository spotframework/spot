<?php
namespace Spot\Inject\Impl;

use SplFixedArray;

class SingletonPool extends SplFixedArray {
    public function link() {
        return new LinkedPool($this);
    }
}