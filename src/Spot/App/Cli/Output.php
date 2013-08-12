<?php
namespace Spot\App\Cli;

interface Output {
    /**
     * Clear output buffer
     * 
     * @return string the output buffer
     */
    function clear();
    
    function write($output);
    
    function printf($format/*, ... */);
    
    function writeln($output);
    
    function flush();
}