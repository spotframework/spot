<?php
namespace Spot\Inject\Impl\Binding;

use Spot\Inject\Key;
use Spot\Inject\Impl\Binding;

abstract class AbstractBinding implements Binding {
    private $key;

    public function __construct(Key $key) {
        $this->key = $key;
    }

    public function getKey() {
        return $this->key;
    }
}