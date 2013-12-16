<?php
namespace Spot\Inject\Bindings;

use Spot\Inject\Binding;
use Spot\Inject\Key;

class InjectorBinding extends Binding {
    public function __construct() {
        parent::__construct(Key::ofType("Spot\\Inject\\Injector"));
    }

    public function __toString() {
        return "Injector";
    }
}
