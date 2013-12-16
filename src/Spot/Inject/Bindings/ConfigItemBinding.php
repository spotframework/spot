<?php
namespace Spot\Inject\Bindings;

use Spot\Inject\BindingVisitor;
use Spot\Inject\Key;
use Spot\Inject\Named;

class ConfigItemBinding extends ConfigBinding {
    private $dependencies;

    public function __construct($name, $source, array $dependencies) {
        parent::__construct($name, $source);

        $this->dependencies = $dependencies;
    }

    public function getDependencies() {
        return $this->dependencies;
    }

    public function __toString() {
        return "";
    }
}
