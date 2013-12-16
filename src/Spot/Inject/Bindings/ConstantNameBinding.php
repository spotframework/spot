<?php
namespace Spot\Inject\Bindings;

use Spot\Inject\Key;

class ConstantNameBinding extends ConstantBinding {
    private $name;

    public function __construct(Key $key, $name) {
        parent::__construct($key);

        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    public function __toString() {
        return "";
    }
}
