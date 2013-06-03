<?php
namespace Spot\Code;

interface CodeWriter {
    function write($code);
    
    function writeln($code);
    
    function newLine();
    
    function indent($step = 1);
    
    function outdent($step = 1);
    
    function writeValue($value);
    
    function __toString();
}