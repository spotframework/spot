<?php
namespace Spot\Inject\Bindings;

use Spot\Inject\Binding;
use Spot\Inject\Key;
use Spot\Inject\Named;

abstract class ConfigBinding extends Binding {
    private $name,
            $source;

    public function __construct($name, $source) {
        parent::__construct(Key::ofConstant(Named::name($name)));

        $this->name = $name;
        $this->source = $source;
    }

    public function getName() {
        return $this->name;
    }

    public function getSource() {
        return $this->source;
    }
}
