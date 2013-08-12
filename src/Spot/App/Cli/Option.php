<?php
namespace Spot\App\Cli;

/** 
 * @Annotation
 */
class Option {
    /**
     * Option name
     * 
     * @var string
     */
    public $value;
    
    /**
     * Option alias
     * 
     * @var string
     */
    public $alias;
    
    /**
     * Option help usages
     * 
     * @var string
     */
    public $help;
}