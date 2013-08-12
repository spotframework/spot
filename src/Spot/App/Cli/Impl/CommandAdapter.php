<?php
namespace Spot\App\Cli\Impl;

use Spot\App\Cli\Argv;
use Spot\Reflect\Method;
use Spot\Reflect\Parameter;
use Spot\Inject\Injector;
use Spot\App\Cli\InvalidCommandUsageException;
use Spot\App\Cli\Output;

class CommandAdapter {
    private $method,
            $injector;

    public function __construct(
            Method $method,
            Injector $injector) {
        $this->method = $method;
        $this->injector = $injector;
    }
    
    public function invoke(Argv $argv, Output $output) {
        $arguments = [];
        $params = $this->method->getParameters();
        $invalidOptions = [];
        foreach($params as $param) {
            try {
                $arguments[] = $this->bindParameter($param, $argv, $output);
            } catch(InvalidOptionException $e) {
                $invalidOptions[] = $e;
            }
        }

        if(count($invalidOptions) > 0) {
            $message = $this->method->getAnnotation("Spot\App\Cli\Command")->help;
            if(!$message) {
                $message = "usage: \n";
                $optionals = $requireds = [];
                foreach($this->method->getParameters() as $param) {
                    $name = $this->getOptionName($param);
                    $help = ($opt = $param->getAnnotation("Spot\App\Cli\Option")) && $opt->help
                            ? $opt->help
                            : "";
                    if(($alias = $this->getOptionAlias($param))) {
                        $name .= ", {$alias}";
                    }
                    
                    if($param->isDefaultValueAvailable()) {
                        $optionals[$name] = $help;
                    } else {
                        $requireds[$name] = $help;
                    }
                }
                
                $optPad = max(array_map("strlen", array_keys($optionals + $requireds))) + 4;
                
                foreach($requireds as $name => $help) {                    
                    $message .= sprintf("  %-{$optPad}s%s\n", $name, $help);
                }
                
                foreach($optionals as $name => $help) {
                    $message .= sprintf(" %-{$optPad}s %s\n", "[{$name}]", $help);
                }
            }
            
            throw new InvalidCommandUsageException(
                $argv->getCommand(), 
                wordwrap($message, 80)
            );
        }
        
        return $this->method->invokeArgs(
            $this->method->isStatic()
                ? null
                : $this->injector->getInstance($this->method->class), 
            $arguments
        );
    }
    
    public function getOptionName(Parameter $param) {
        $opt = $param->getAnnotation("Spot\App\Cli\Option");
        if($opt && $opt->value) {
            return $opt->value;
        }
        
        return strlen($param->name) === 1 
            ? "-{$param->name}" 
            : "--{$param->name}";
    }
    
    public function getOptionAlias(Parameter $param) {
        return ($opt = $param->getAnnotation("Spot\App\Cli\Option")) 
                ? $opt->alias
                : "";
    }
    
    public function bindParameter(Parameter $param, Argv $argv, Output $output) {
        if(($class = $param->getClass())) {
            if($class->isInstance($argv)) {
                return $argv;
            }
            
            if($class->isInstance($output)) {
                return $output;
            }
        }

        $name = $this->getOptionName($param);
        $alias = $this->getOptionAlias($param);
        $options = $argv->getOptions();
        if(!isset($options[$name]) && !isset($options[$alias])) {
            if(!$param->isDefaultValueAvailable()) {
                throw new InvalidOptionException($name, false, $param->isArray());
            } else {
                $option = $param->getDefaultValue();
            }
        } else {
            $option = isset($options[$name]) 
                ? $options[$name] 
                : $options[$alias];
        }

        if($param->isArray() && !is_array($option)) {
            if($option === true) {
                throw new InvalidOptionException($name, $param->isDefaultValueAvailable(), true);
            }

            $option = (array)$option;
        }

        return $option;
    }
}