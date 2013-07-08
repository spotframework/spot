<?php
namespace Spot\Inject\Impl;

use Spot\Spot;
use Spot\Inject\Named;
use Spot\Inject\Provides;
use Spot\Inject\Singleton;
use Spot\Code\CodeStorage;
use Spot\Reflect\Reflection;
use Doctrine\Common\Cache\Cache;

class BuiltInModule {
    private $constants,
            $cache,
            $reflection,
            $codeStorage;
    
    public function __construct(
            array $constants,
            Cache $cache,
            Reflection $reflection, 
            CodeStorage $codeStorage) {
        $this->constants = $constants;
        $this->cache = $cache;
        $this->reflection = $reflection;
        $this->codeStorage = $codeStorage;
    }
    
    /** @Provides("Doctrine\Common\Cache\Cache") @Singleton */
    function provideCache() {
        return $this->cache;
    }
    
    /** @Provides("Spot\Reflect\Reflection") @Singleton */
    function provideReflection() {
        return $this->reflection;
    }
    
    /** @Provides("Spot\Code\CodeStorage") @Singleton */
    function provideCodeStorage() {
        return $this->codeStorage;
    }
    
    /** @Provides @Named("app.path") @Singleton */
    function providePath() {
        return $this->constants["app.path"];
    }
    
    /** @Provides @Named("app.dump-dir") @Singleton */
    function provideDumpDir() {
        return $this->constants["app.dump-dir"];
    }
    
    /** @Provides @Named("app.mode") @Singleton */
    function provideMode() {
        return $this->constants["app.mode"];
    }
    
    /** @Provides @Named("app.debug") @Singleton */
    function provideIsDebug(/** @Named("app.mode") */$mode) {
        return $mode === Spot::DEV_MODE;
    }
}