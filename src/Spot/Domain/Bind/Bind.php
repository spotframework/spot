<?php
namespace Spot\Domain\Bind;

/** @Annotation */
final class Bind {    
    /**
     * Binding name
     * 
     * @var string
     */
    public $value;
    
    /**
     * 
     * 
     * @var boolean
     */
    public $multiple = false;
    
    /**
     * List of binding value formatters
     * Formatters will be applied before the value is binded
     * 
     * @var array<\Spot\Domain\Bind\Formatter>
     */
    public $formatters = [];
}
