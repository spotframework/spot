<?php
namespace Spot\Inject;

/** @Annotation */
class Intercept {
    public $value;

    public function getMatchers() {
        return is_array($this->value)
            ? $this->value
            : [$this->value];
    }
}
