<?php
namespace Spot\Inject\Bindings;

use Spot\Inject\Binding;
use Spot\Reflect\Method;

abstract class ModuleBinding extends Binding {
    public abstract function getSource();
}
