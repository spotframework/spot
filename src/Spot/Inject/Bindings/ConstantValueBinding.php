<?php
namespace Spot\Inject\Bindings;

use Spot\Inject\Key;

class ConstantValueBinding extends ConstantBinding {
    private $value;

    public function __construct(Key $key, $value) {
        parent::__construct($key);

        $this->value = $value;
    }

    public function getValue() {
        return $this->value;
    }

    public function __toString() {
        return "const ".$this->value;
    }
}
