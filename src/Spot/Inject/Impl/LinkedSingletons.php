<?php
namespace Spot\Inject\Impl;

class LinkedSingletons extends Singletons {
    private $super;

    public function __construct(Singletons $super) {
        parent::__construct();

        $this->super = $super;
    }

    public function offsetGet($index) {
        return
            $index >= $this->super->getSize()
                ? parent::offsetGet($index - $this->super->getSize())
                : $this->super[$index];
    }

    public function offsetSet($index, $value) {
        if($index >= $this->super->getSize()) {
            parent::offsetSet($index - $this->super->getSize(), $value);
        } else {
            $this->super[$index] = $value;
        }
    }

    public function getSize() {
        return $this->super->getSize() + parent::getSize();
    }

    public function setSize($size) {
        parent::setSize($size - $this->super->getSize());
    }

    public function count() {
        return $this->getSize();
    }
}
