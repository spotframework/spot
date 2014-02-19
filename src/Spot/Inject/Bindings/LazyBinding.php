<?php
namespace Spot\Inject\Bindings;

use Spot\Inject\Binding;

class LazyBinding extends Binding {
    public function __toString() {
        return "";
    }
}
