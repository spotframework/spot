<?php
namespace Spot\App\Cli\Impl;

use Spot\Inject\Named;

class AppUsagePrinter {
    private $descriptions;
    
    public function __construct(
            /** @Named("app.cli.command-descriptions") */$descriptions) {
        $this->descriptions = $descriptions;
    }
    
    public function render() {
        static $leftPad = 2, $midPad = 4;
        
        $usage = "available commands:\n";
        $cmdPad = max(array_map("strlen", array_keys($this->descriptions)));
        
        foreach($this->descriptions as $cmdName => $description) {
            $usage .= str_repeat(" ", $leftPad);
            
            $usage .= sprintf("%-{$cmdPad}s", $cmdName);
            $usage .= str_repeat(" ", $midPad);
            
            $description = wordwrap($description, 80 - $cmdPad - $leftPad - $midPad);
            $description = str_replace("\n", "\n".str_repeat(" ", $cmdPad + $leftPad + $midPad), $description);
            
            $usage .= sprintf("%s\n", $description);
        }

        return $usage;
    }
}