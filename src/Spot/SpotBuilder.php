<?php
namespace Spot;

use Spot\Code\CodeStorage;
use Spot\Code\Impl\DoctrineCacheStorage;
use Spot\Reflect\Impl\ReflectionImpl;
use Spot\Code\Impl\FileStorage;
use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\ApcCache;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\XcacheCache;

class SpotBuilder {    
    private $cache,
            $constants = [],
            $codeStorage;
    
    public function __construct($appPath, $dumpDir = null) {
        $this->cache = new ArrayCache();
        $this->constants = [
            "app.path" => $appPath = realpath($appPath),
            "app.dump-dir" => $dumpDir ?: $appPath."/dump",
        ];
    }
    
    public function setCodeStorage(CodeStorage $codeStorage) {
        $this->codeStorage = $codeStorage;
        
        return $this;
    }
    
    public function setMode($mode) {
        $this->constants["app.mode"] = $mode;
        
        return $this;
    }
    
    public function setCache(Cache $cache) {
        $this->cache = $cache;
    }
    
    public function buildDev() {
        $cache = new ArrayCache();        
        $storage = new DoctrineCacheStorage("SpotGen", $cache);
        
        $this->setCodeStorage($storage);
        $this->setMode(Spot::DEV_MODE);
        
        return $this;
    }
    
    public function buildProd() {        
        $storage = new FileStorage("SpotGen", $this->constants["app.dump-dir"]);        
        $this->setCodeStorage($storage);
        $this->setMode(Spot::PROD_MODE);
        
        if(extension_loaded("apc")) {
            $this->setCache(new ApcCache());
        } else if(extension_loaded("xcache")) {
            $this->setCache(new XcacheCache());
        }
        
        return $this;
    }
    
    public function get() {
        return new Spot(
            $this->cache,
            $this->constants, 
            ReflectionImpl::create($this->cache), 
            $this->codeStorage
        );
    }
}