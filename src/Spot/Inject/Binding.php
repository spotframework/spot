<?php
namespace Spot\Inject;

abstract class Binding {
    private $k;

    public function __construct(Key $key) {
        $this->k = $key;
    }

    /**
     * @return Key
     */
    public function getKey() {
        return $this->k;
    }

    public function accept(BindingVisitor $visitor) {
        $visitor->visit($this);
    }

    public abstract function __toString();
}
