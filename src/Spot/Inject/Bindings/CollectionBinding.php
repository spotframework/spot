<?php
namespace Spot\Inject\Bindings;

use Spot\Inject\Binding;

class CollectionBinding extends Binding {
    private $elements = [];

    public function addElement(ModuleBinding $element) {
        $this->elements[] = $element;
    }

    public function getElements() {
        return $this->elements;
    }

    public function __toString() {
        return "";
    }
}
