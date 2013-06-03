<?php
namespace Spot\App;

use Spot\Inject\Named;
use Spot\Inject\Provides;

class AppModule {    
    static function getReflection() {
        static $reflections = [];
        $class = get_called_class();
        if(!isset($reflections[$class])) {
            $reflections[$class] = new \ReflectionClass(get_called_class());
        }
        
        return $reflections[$class];
    }
    
    /** @Provides(Provides::ELEMENT) @Named("app.module.namespaces") */
    static function provideNamespace() {
        return self::getReflection()->getNamespaceName();
    }
    
    /** @Provides(Provides::ELEMENT) @Named("app.module.paths")*/
    static function providePath() {
        return dirname(self::getReflection()->getFileName());
    }
}