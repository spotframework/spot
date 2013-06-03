<?php
namespace Spot\Inject\Impl\Binder\Config;

class ConfigItem {
    private $value,
            $source;

    public function __construct($value, $source) {
        $this->value = $value;
        $this->source = $source;
    }

    public function getValue() {
        return $this->value;
    }

    public function getSource() {
        return $this->source;
    }
}