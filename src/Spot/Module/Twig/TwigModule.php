<?php
namespace Spot\Module\Twig;

use Spot\Inject\Provides;
use Spot\Inject\Named;
use Spot\Inject\Singleton;

class TwigModule {
    /** @Provides("Twig_Environment") @Singleton */
    static function provideEnv(
            \Twig_LoaderInterface $loader,
            /** @Named("app.debug") */$debug,
            /** @Named("twig.cache") */$cache) {
        $env = new \Twig_Environment($loader, [
            "debug" => $debug,
            "cache" => $cache,
        ]);

        return $env;
    }

    /** @Provides @Named("twig.cache") */
    static public function provideCache(
            /** @Named("app.debug") */$debug) {
        return $debug ? false : (sys_get_temp_dir() );
    }

    /** @Provides("Twig_LoaderInterface") */
    static function provideLoader(
            /** @Named("app.module.namespaces") */array $namespaces = [],
            /** @Named("app.module.paths") */array $paths = []) {
        $loader = new \Twig_Loader_Filesystem("/");
        foreach(array_combine($namespaces, $paths) as $ns => $path) {
            $loader->addPath($path, $ns);
        }

        return $loader;
    }

    /** @Provides(Provides::ELEMENT) @Named("app.web.view-renderers") */
    static public function provideRenderer(TwigRenderer $renderer) {
        return $renderer;
    }
}
