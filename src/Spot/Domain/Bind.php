<?php
namespace Spot\Domain;

/**
 * @Annotation
 * @Target("METHOD")
 */
final class Bind {
    public $value;

    public $multi = false;
}
