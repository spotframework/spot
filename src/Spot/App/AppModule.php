<?php
namespace Spot\App;

use Spot\Inject\Named;
use Spot\Inject\Provides;

class AppModule {
    static public function getReflection() {
        static $r = [];

        $n = get_called_class();
        if(!isset($r[$n])) {
            $r[$n] = new \ReflectionClass($n);

        }

        return $r[$n];
    }

    /** @Provides(Provides::ELEMENT) @Named("app.module.paths") */
    static public function providePath() {
        return dirname(static::getReflection()->getFilename());
    }

    /** @Provides(Provides::ELEMENT) @Named("app.module.namespaces") */
    static public function provideNamespace() {
        return static::getReflection()->getNamespaceName();
    }
}
