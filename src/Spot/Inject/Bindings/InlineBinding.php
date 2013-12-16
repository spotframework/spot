<?php
namespace Spot\Inject\Bindings;

use Spot\Inject\Binding;
use Spot\Inject\Key;
use Spot\Reflect\Type;

class InlineBinding extends Binding {
    private $type,
            $dependencies;

    public function __construct(Type $type, array $dependencies) {
        parent::__construct(Key::ofType($type->name));

        $this->type = $type;
        $this->dependencies = $dependencies;
    }

    public function getType() {
        return $this->type;
    }

    public function getDependencies() {
        return $this->dependencies;
    }

    public function __toString() {
        return "new \\".$this->getType();
    }
}
