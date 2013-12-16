<?php
namespace Spot\Inject\Bindings;

use Spot\Inject\Binding;
use Spot\Inject\BindingVisitor;
use Spot\Inject\Key;

class OptionalBinding extends Binding {
    private $default;

    public function __construct(Key $key, ConstantBinding $default) {
        parent::__construct($key);

        $this->default = $default;
    }

    public function getDefault() {
        return $this->default;
    }

    public function __toString() {
        return "";
    }
}
