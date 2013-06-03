<?php
namespace Spot\Reflect\Impl;

class PhpLoader {    
    private $loaded = [];
    
    public function load($path) {
        if(isset($this->loaded[$path])) return;
        
        $dir = new \RecursiveDirectoryIterator($path);
        $rec = new \RecursiveIteratorIterator($dir);
        $filtered = new PhpFilterIterator($rec);
        
        foreach($filtered as $file) {
            require_once $file;
        }
        
        $this->loaded[$path] = true;
    }
}