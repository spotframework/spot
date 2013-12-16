<?php
namespace Spot\Inject;

/**
 * @Annotation
 * @Target("METHOD")
 */
final class Provides {
    const ELEMENT = "array";
    const CONSTANT = "const";

    public $value = Provides::CONSTANT;

    public $overrides = false;
}
