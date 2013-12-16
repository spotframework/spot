<?php
namespace Spot\Inject\Bindings;

use Spot\Inject\Binding;

class SingletonBinding extends ModuleBinding {
    private $delegate,
            $index;

    public function __construct(ProviderMethodBinding $delegate) {
        parent::__construct($delegate->getKey());

        $this->delegate = $delegate;
    }

    public function setIndex($index) {
        $this->index = $index;
    }

    public function getIndex() {
        return $this->index;
    }

    public function getDelegate() {
        return $this->delegate;
    }

    public function __toString() {
        return "@Singleton ".$this->delegate;
    }

    public function getSource() {
        return $this->delegate->getSource();
    }
}
