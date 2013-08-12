<?php
namespace Spot\App\Cli\Impl;

use Spot\App\Cli\Argv;

class CommandNotFoundException extends \RuntimeException {
    private $args;
    
    public function __construct(Argv $args, $message = "") {
        parent::__construct($message);
        
        $this->args = $args;
    }
}