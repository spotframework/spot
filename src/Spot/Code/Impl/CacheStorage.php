<?php
namespace Spot\Code\Impl;

use Spot\Cache\CacheManager;
use Spot\Code\CodeStorage;

class CacheStorage implements CodeStorage {
    private $ns,
            $cache;
    
    public function __construct($namespace, CacheManager $cache) {
        $this->ns = $namespace;
        $this->cache = $cache;
    }
    
    public function load($name) {
        $fqcn = $this->ns.'\\'.$name;
        if(class_exists($fqcn, false)) {
            return $fqcn;
        }
        
        $code = $this->cache->get($name);
        if($code) {
            eval($code);
            
            return $fqcn;
        }
    }
    
    public function store($name, $code) {
        $this->cache->set($name, 'namespace '.$this->ns.';

'.$code);
        
        return $this->load($name);
    }

    public function bust($name) {
        $this->cache->delete($name);
    }
}