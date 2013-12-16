<?php
namespace Spot\Inject\Bindings;

use Spot\Inject\Binding;

class LateBinding extends Binding {
    public function __toString() {
        return "";
    }
}
