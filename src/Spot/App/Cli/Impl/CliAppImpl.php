<?php
namespace Spot\App\Cli\Impl;

use Spot\Inject\Named;
use Spot\App\Cli\Argv;
use Spot\App\Cli\CliApp;
use Spot\App\Cli\InvalidCommandUsageException;

class CliAppImpl implements CliApp {    
    private $router,
            $usage;
    
    public function __construct(
            Router $router,
            AppUsagePrinter $usage) {
        $this->router = $router;
        $this->usage = $usage;
    }
    
    public function handle(Argv $argv) {
        $output = new OutputImpl();
        
        try {    
            $command = $this->router->resolve($argv);
            
            $command->invoke($argv, $output);
            
            return $output;
        } catch(CommandNotFoundException $e) {
            $output->write($this->usage->render());
        } catch(InvalidCommandUsageException $e) {
            $output->writeln($e->getMessage());
        }
    }    
}