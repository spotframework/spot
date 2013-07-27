<?php
namespace Spot\App\Cli;

use ArrayObject;

class Args {
    private $command,
            $options;
    
    public function __construct($command, array $options) {
        $this->command = $command;
        $this->options = $options;
    } 
   
    public function getCommand() {
        return $this->command;
    }
    
    public function getOptions() {
        return $this->options;
    }
    
    static function createFromGlobal() {
        $argv = $GLOBALS["argv"];
        array_shift($argv); //remove script name
        
        return self::create($argv);
    }
    
    static function create(array $argv) {
        $command = array_shift($argv);
        $options = [];
        for($i = 0, $c = count($argv); $i < $c; ++$i) {
            if(isset($argv[$i][0]) && $argv[$i][0] === "-") {
                $option = $argv[$i];
                $options[$option] = true;
                while(isset($argv[$i + 1]) && $argv[$i + 1][0] !== "-") {
                    ++$i;
                    if(count($options[$option]) === 1) {
                        if($options[$option] === true) {
                            $options[$option] = $argv[$i];
                        } else {
                            $options[$option] = [
                                $options[$option],
                                $argv[$i]
                            ];
                        }
                    } else {
                        $options[$option][] = $argv[$i];
                    }
                }
            }
        }
        
        return new self($command, $options);
    }
}