<?php
namespace Spot\Code\Impl;

use Spot\Code\CodeStorage;
use Doctrine\Common\Cache\Cache;

class DoctrineCacheStorage implements CodeStorage {
    private $ns,
            $cache;
    
    public function __construct($namespace, Cache $cache) {
        $this->ns = $namespace;
        $this->cache = $cache;
    }
    
    public function load($name) {
        $fqcn = $this->ns.'\\'.$name;
        if(class_exists($fqcn, false)) {
            return $fqcn;
        }
        
        $code = $this->cache->fetch($name);
        if($code) {
            eval($code);
            
            return $fqcn;
        }
    }
    
    public function store($name, $code) {
        $this->cache->save($name, 'namespace '.$this->ns.';

'.$code);
        
        return $this->load($name);
    }

    public function bust($name) {
        $this->cache->delete($name);
    }
}