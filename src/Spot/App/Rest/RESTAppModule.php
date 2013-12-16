<?php
namespace Spot\App\REST;

use Spot\App\REST\Impl\ContentNegotiationRenderer;
use Spot\App\REST\Impl\RESTAppImpl;
use Spot\App\REST\Impl\ResourceScanner;
use Spot\Inject\Provides;
use Spot\Inject\Singleton;
use Spot\Inject\Named;

class RESTAppModule {
    /** @Provides("Spot\App\REST\RESTApp") @Singleton */
    static public function provideRESTApp(RESTAppImpl $app) {
        return $app;
    }

    /** @Provides(Provides::ELEMENT) @Named("app.web.view-renderers") */
    static public function provideRenderer(
            ContentNegotiationRenderer $renderer) {
        return $renderer;
    }

    /** @Provides("Spot\App\Web\Impl\Router\InvertedIndex\ControllerScanner") */
    static public function provideResourceScanner(ResourceScanner $scanner) {
        return $scanner;
    }
}
