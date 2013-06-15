<?php
namespace Spot\Inject\Impl;

use Spot\Spot;
use Spot\Inject\Named;
use Spot\Inject\Provides;
use Spot\Inject\Singleton;
use Spot\Reflect\Reflection;
use Spot\Code\CodeStorage;

class BuiltInModule {
    private $constants,
            $reflection,
            $codeStorage;
    
    public function __construct(
            array $constants,
            Reflection $reflection, 
            CodeStorage $codeStorage) {
        $this->constants = $constants;
        $this->reflection = $reflection;
        $this->codeStorage = $codeStorage;
    }
    
    /** @Provides("Spot\Reflect\Reflection") @Singleton */
    function provideReflection() {
        return $this->reflection;
    }
    
    /** @Provides("Spot\Code\CodeStorage") @Singleton */
    function provideCodeStorage() {
        return $this->codeStorage;
    }
    
    /** @Provides @Named("app.path") */
    function providePath() {
        return $this->constants["app.path"];
    }
    
    /** @Provides @Named("app.dump-dir") */
    function provideDumpDir() {
        return $this->constants["app.dump-dir"];
    }
    
    /** @Provides @Named("app.mode") */
    function provideMode() {
        return $this->constants["app.mode"];
    }
    
    /** @Provides @Named("app.debug") */
    function provideIsDebug(/** @Named("app.mode") */$mode) {
        return $mode === Spot::DEV_MODE;
    }
}