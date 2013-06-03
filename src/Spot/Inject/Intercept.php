<?php
namespace Spot\Inject;

/** @Annotation */
class Intercept {
    const METHOD = "METHOD";
    const CONSTRUCTOR = "CONSTRUCTOR";

    public $value;

    public $target = self::METHOD;

    public function getMatchers() {
        return is_array($this->value)
            ? $this->value
            : [$this->value];
    }
}