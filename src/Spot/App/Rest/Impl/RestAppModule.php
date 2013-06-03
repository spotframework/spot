<?php
namespace Spot\App\Rest\Impl;

use Spot\Inject\Named;
use Spot\Inject\Provides;
use Spot\App\Web\WebApp;

class RestAppModule {
    /** @Provides("Spot\App\Web\Impl\ControllerMappingProvider") */
    static function provideMappingProvider(RestMappingProvider $rest) {
        return $rest;
    }

    /** @Provides(Provides::ELEMENT) @Named(WebApp::VIEW_RENDERERS) */
    static function provideContentNegotiatorRenderer(ContentNegotiationRenderer $renderer) {
        return $renderer;
    }
}