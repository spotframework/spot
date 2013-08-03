<?php
namespace Spot\App\Rest\Impl;

use Spot\Inject\Named;
use Spot\Inject\Provides;
use Spot\App\Web\WebApp;
use Spot\Reflect\Reflection;
use Spot\Reflect\Match;

class RestAppModule {    
    /** @Provides @Named("app.rest.resources") */
    static public function provideResources(
            Reflection $reflection,
            /** @Named("app.module.namespaces") */array $namespaces) {
        $resources = [];
        $matcher = Match::annotatedWith("Spot\App\Rest\Resource");
        foreach($namespaces as $ns) {
            foreach($reflection->find($ns, $matcher) as $resource) {
                $atResource = $resource->getAnnotation("Spot\App\Rest\Resource");
                
                $resources[$atResource->value] = $resource;
            }
        }
        return $resources;
    }
    
    /** @Provides("Spot\App\Web\Impl\ControllerMappingProvider") */
    static function provideMappingProvider(RestMappingProvider $rest) {
        return $rest;
    }

    /** @Provides(Provides::ELEMENT) @Named("app.web.view-renderers") */
    static function provideContentNegotiatorRenderer(ContentNegotiationRenderer $renderer) {
        return $renderer;
    }
}