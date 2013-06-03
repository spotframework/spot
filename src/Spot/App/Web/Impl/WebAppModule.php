<?php
namespace Spot\App\Web\Impl;

use Spot\App\Web\Impl\Strategy\InvertedIndex\InvertedIndexModule;
use Spot\App\Web\Impl\WebAppImpl;
use Spot\Inject\Provides;
use Spot\Inject\Named;
use Spot\Inject\Lazy;

class WebAppModule {
    use InvertedIndexModule;

    /** @Provides("Spot\App\Web\Impl\MappingProvider") */
    static function provideMappingProvider(ControllerMappingProvider $provider) {
        return $provider;
    }

    /** @Provides("Spot\App\Web\WebApp") */
    static function provideWebApp(WebAppImpl $app) {
        return $app;
    }

    /** @Provides @Named("app.hash") */
    static function provideHash(
            /** @Named("app.module.namespaces") */array $namespaces) {
        return md5(implode($namespaces));
    }
}