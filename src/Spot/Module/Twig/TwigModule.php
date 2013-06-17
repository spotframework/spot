<?php
namespace Spot\Module\Twig;

use Spot\Spot;
use Spot\Inject\Named;
use Spot\Inject\Provides;
use Spot\Inject\Singleton;
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
            /** @Named("app.dump-dir") */$dumpDir) {
        return $mode === Spot::PROD_MODE ? "{$dumpDir}/twig" : false;
    }

    /** @Provides("Twig_LoaderInterface") */
    static function provideLoader(
            /** @Named("twig.paths") */array $paths = ["/"],
            /** @Named("app.module.paths") */array $modulePaths = [],
            /** @Named("app.module.namespaces") */array $moduleNamespaces = []) {        
        $loader = new \Twig_Loader_Filesystem($paths);
        foreach(array_combine($moduleNamespaces, $modulePaths) as $namespace => $path) {
            $loader->addPath($path, $namespace);
        }
        
        return $loader;
    }
    
    /** @Provides(Provides::ELEMENT) @Named("twig.extensions") @Singleton */
    static function provideSpotExtension(SpotExtension $ext) {
        return $ext;
    }
}