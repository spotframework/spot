<?php
namespace Spot\Module\Twig;

use Spot\Spot;
use Spot\Inject\Injector;
use Spot\Inject\Key;
use Spot\Inject\Named;
use Spot\Inject\Provides;
use Spot\Inject\Singleton;
use Spot\Reflect\Reflection;
use Spot\App\Web\WebApp;

class TwigModule {
    /** @Provides(Provides::ELEMENT) @Named(WebApp::VIEW_RENDERERS) */
    static function provideTwigRenderer(TwigRenderer $renderer) {
        return $renderer;
    }

    /** @Provides("Twig_Environment") @Singleton */
    static function provideEnvironment(
            \Twig_LoaderInterface $loader,
            /** @Named("twig.debug") */$debug = true,
            /** @Named("twig.cache") */$cache = false,
            /** @Named("twig.extensions") */array $extensions = []) {
        $env = new \Twig_Environment($loader, [
            'debug' => $debug,
            'cache' => $cache,
        ]);

        foreach($extensions as $extension) {
            $env->addExtension($extension);
        }

        return $env;
    }

    /** @Provides @Named("twig.cache") */
    static function provideIsCache(
            /** @Named("app.mode") */$mode,
            Injector $injector) {

        return $mode === Spot::PROD_MODE
            ? $injector->get(Key::ofConstant(Named::name("app.dump-dir")))."/twig"
            : false;
    }

    /** @Provides("Twig_LoaderInterface") */
    static function provideLoader(
            /** @Named("twig.paths") */array $paths = ['/']) {
        return new \Twig_Loader_Filesystem($paths);
    }
}