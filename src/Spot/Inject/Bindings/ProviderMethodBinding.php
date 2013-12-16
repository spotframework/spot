<?php
namespace Spot\Inject\Bindings;

use Spot\Inject\Binding;
use Spot\Inject\Key;
use Spot\Reflect\Method;

class ProviderMethodBinding extends ModuleBinding {
    private $index,
            $method,
            $dependencies;

    public function __construct(
            Key $key,
            $index,
            Method $method,
            array $dependencies) {
        parent::__construct($key);

        $this->index = $index;
        $this->method = $method;
        $this->dependencies = $dependencies;
    }

    public function getIndex() {
        return $this->index;
    }

    public function getMethod() {
        return $this->method;
    }

    public function getSource() {
        return $this->method->getFileName().":".$this->method->getStartLine();
    }

    public function getDependencies() {
        return $this->dependencies;
    }

    public function __toString() {
        return "";
    }
}
