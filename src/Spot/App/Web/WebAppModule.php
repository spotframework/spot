<?php
namespace Spot\App\Web;

use Spot\App\Web\Impl\Router\InvertedIndex\InvertedIndexFactory;
use Spot\App\Web\Impl\WebAppImpl;
use Spot\Inject\Provides;
use Spot\Inject\Singleton;
use Spot\Inject\Named;

class WebAppModule {
    /** @Provides("Spot\App\Web\Router", overrides = true) @Singleton */
    static public function provideRouter(InvertedIndexFactory $factory) {
        return $factory->get();
    }

    /** @Provides("Spot\App\Web\WebApp") @Singleton */
    static public function provideWebApp(WebAppImpl $app) {
        return $app;
    }

    /** @Provides @Named("app.hash") @Singleton */
    static public function provideAppHash(
        /** @Named("app.module.namespaces") */array $namespaces = []) {
        return md5(implode($namespaces));
    }
}
