<?php
namespace Spot;

use Spot\Code\CodeStorage;
use Spot\Code\Impl\CacheStorage;
use Spot\Cache\CacheManager;
use Spot\Cache\Storage\DummyStorage;
use Spot\Reflect\Impl\ReflectionImpl;
use Spot\Code\Impl\FileStorage;

class SpotBuilder {    
    private $constants = [],
            $codeStorage;
    
    public function __construct($appPath) {
        $this->constants = [
            "app.path" => realpath($appPath),
            "app.dump-dir" => realpath(sys_get_temp_dir()),
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
    
    public function buildDev() {
        $storage = new DummyStorage();
        $cache = new CacheManager("SpotGen", $storage);
        $codeStorage = new CacheStorage("SpotGen", $cache);
        
        $this->setCodeStorage($codeStorage);
        $this->setMode(Spot::DEV_MODE);
        
        return $this;
    }
    
    public function buildProd($dumpDir = null) {
        if($dumpDir) {
            $dumpDir = realpath($dumpDir);

            $this->constants["app.dump-dir"] = $dumpDir;
        }
        
        $storage = new FileStorage("SpotGen", $this->constants["app.dump-dir"]);
        
        $this->setCodeStorage($storage);
        $this->setMode(Spot::PROD_MODE);
        
        return $this;
    }
    
    public function get() {
        return new Spot(
            $this->constants, 
            ReflectionImpl::create(), 
            $this->codeStorage
        );
    }
}