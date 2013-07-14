<?php
namespace Spot\Module\Twig;

use Spot\Spot;
use Spot\Inject\Named;
use Spot\Inject\Provides;
use Spot\Inject\Singleton;

class TwigModule {
    /** @Provides(Provides::ELEMENT) @Named("app.web.view-renderers") */
    static function provideTwigRenderer(TwigRenderer $renderer) {
        return $renderer;
    }

    /** @Provides("Twig_Environment") @Singleton */
    static function provideEnvironment(
            \Twig_LoaderInterface $loader,
            /** @Named("app.debug") */$debug,
            /** @Named("app.dump-dir") */$dumpDir,
            /** @Named("twig.extensions") */array $extensions = []) {
        $env = new \Twig_Environment($loader, [
            "debug" => $debug,
            "cache" => $debug ? false : "{$dumpDir}/twig",
        ]);

        $env->setExtensions($extensions);

        return $env;
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