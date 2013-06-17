<?php
namespace Spot\Code;

interface CodeStorage {
    /**
     * @param string $name
     * 
     * @return string Fully qualified class name
     */
    function load($name);
    
    /**
     * @param string $name
     * @param string $code
     * 
     * @return string Fully qualified class name
     */
    function store($name, $code);
    
    /**
     * @param string $name
     */
    function bust($name);
}