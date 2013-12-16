<?php
namespace Spot\Inject\Bindings;

use Spot\Inject\BindingVisitor;

class ConfigCollectionBinding extends ConfigBinding {
    private $elements;

    public function __construct($name, $source, array $elements) {
        parent::__construct($name, $source);

        $this->elements = $elements;
    }

    public function getElements() {
        return $this->elements;
    }

    public function __toString() {
        return "";
    }
}
