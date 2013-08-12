<?php
namespace Spot\App\Cli;

class InvalidCommandUsageException extends \RuntimeException {
    private $command;
    
    public function __construct($command, $message = "") {
        parent::__construct($message);
        
        $this->command = $command;
    }
    
    public function getCommand() {
        return $this->command;
    }
}