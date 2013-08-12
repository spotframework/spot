<?php
namespace Spot\App\Cli\Impl;

use Spot\Inject\Named;
use Spot\Inject\Provides;
use Spot\Reflect\Match;
use Spot\Reflect\Reflection;
use Spot\App\Cli\Impl\CliAppImpl;


class CliModule {
    /** @Provides @Named("app.cli.cwd") */
    static function getCurrentWorkingDirectory() {
        return getcwd();
    }
    
    /** @Provides("Spot\App\Cli\CliApp") */
    static function provideApp(CliAppImpl $app) {
        return $app;
    }
    
    /** @Provides @Named("app.cli.commands") */
    static function provideCommands(
            Reflection $reflection,
            /** @Named("app.module.namespaces") */array $namespaces) {
        $commands = [];
        $matcher = Match::hasMethodAnnotatedWith("Spot\App\Cli\Command");
        foreach($namespaces as $ns) {
            foreach($reflection->find($ns, $matcher) as $class) {
                foreach($class->getMethods() as $method) {
                    if($method->isAnnotatedWith("Spot\App\Cli\Command")) {
                        $commands[] = $method;
                    }
                }
            }
        }
        
        return $commands;
    }
    
    /** @Provides @Named("app.cli.command-helps") */
    static function provideCommandHelps(/** @Named("app.cli.commands") */$commands) {
        $helps = [];
        foreach($commands as $cmd) {
            $command = $cmd->getAnnotation("Spot\App\Cli\Command");
            
            $helps[$command->value] = $command->help;
        }
        
        return $helps;
    }
    
    /** @Provides @Named("app.cli.command-descriptions") */
    static function provideCommandDescriptions(/** @Named("app.cli.commands") */$commands) {
        $helps = [];
        foreach($commands as $cmd) {
            $command = $cmd->getAnnotation("Spot\App\Cli\Command");
            
            $helps[$command->value] = $command->description;
        }
        
        return $helps;
    }
}