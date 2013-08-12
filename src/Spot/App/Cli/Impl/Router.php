<?php
namespace Spot\App\Cli\Impl;

use Spot\Inject\Named;
use Spot\App\Cli\Argv;
use Spot\Reflect\Method;
use Spot\Inject\Injector;

class Router {
    private $injector,
            $commands;
    
    public function __construct(
            Injector $injector,
            /** @Named("app.cli.commands") */$commands) {
        $this->injector = $injector;
        $this->commands = $commands;
    }
    
    public function resolve(Argv $args) {
        foreach($this->commands as $method) {
            if($this->matches($method, $args)) {
                return new CommandAdapter($method, $this->injector);
            }
        }
        
        throw new CommandNotFoundException($args);
    }
    
    public function matches(Method $method, Argv $args) {
        if($method->getAnnotation("Spot\App\Cli\Command")->value != $args->getCommand()){
            return false;
        }
        
        $options = $args->getOptions();
        foreach($method->getAnnotations("Spot\App\Cli\Option") as $option) {
            if(!isset($options[$option->value], $options[$option->alias])) {
                return false;
            }
        }
        
        return true;
    }
}