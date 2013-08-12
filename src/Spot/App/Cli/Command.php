<?php
namespace Spot\App\Cli;

/** 
 * @Annotation 
 * @Target("METHOD")
 */
class Command {
    /**
     * Command name
     * 
     * @var string 
     */
    public $value = "";
    
    /**
     * Command description
     * 
     * @var string
     */
    public $description;

    /**
     * Command help
     *
     * @var string
     */
    public $help;
}