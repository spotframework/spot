<?php
namespace Spot\Inject;

/** @Annotation */
class Provides {
    const ELEMENT = 'array';
    const CONSTANT = 'const';
    
    public $value = self::CONSTANT;
}