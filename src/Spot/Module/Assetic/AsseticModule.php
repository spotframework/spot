<?php
namespace Spot\Module\Assetic;

use Spot\App\Web\WebApp;
use Spot\Inject\Named;
use Spot\Inject\Provides;
use Spot\Inject\Singleton;
use Assetic\Factory\AssetFactory;
use Assetic\Extension\Twig\AsseticExtension;
use Assetic\Factory\LazyAssetManager;
use Assetic\Factory\Worker\CacheBustingWorker;
use Assetic\FilterManager;
use Assetic\Filter\LessphpFilter;
use Assetic\Filter\ScssphpFilter;
use Assetic\Filter\Yui\CssCompressorFilter;
use Assetic\Filter\Yui\JsCompressorFilter;
use Assetic\Filter\CoffeeScriptFilter;

class AsseticModule {
    /** @Provides("Assetic\Factory\AssetFactory") @Singleton */
    static function provideFactory(
            FilterManager $fm,
            /** @Named("assetic.input") */$root,
            /** @Named("assetic.debug") */$debug = false) {
        $factory = new AssetFactory($root, $debug);
        $factory->setFilterManager($fm);
        $am = new LazyAssetManager($factory);
        
        $factory->addWorker(new CacheBustingWorker($am));
        
        return $factory;
    }
    
    /** @Provides @Named("assetic.filters") @Singleton */
    static function provideFilters(
            /** @Named("assetic.js-filters") */$jsFilters,
            /** @Named("assetic.css-filters") */$cssFilters) {
        return array_merge(explode(",", $jsFilters), explode(",", $cssFilters));
    }
    
    /** @Provides("Assetic\FilterManager") @Singleton */
    static function provideFilterManager(FilterManager $fm,
            /** @Named("assetic.filters") */$filters = []) {
        foreach($filters as $filter) {
            switch(trim($filter)) {
                case "scss":
                    $fm->set("scss", new ScssphpFilter());
                    break;
                case "less":
                    $fm->set("less", new LessphpFilter());
                    break;
            }
        }
        
        return $fm;
    }
    
    /** @Provides(Provides::ELEMENT) @Named("twig.extensions") @Singleton */
    static function provideTwigExtensions(
            AssetFactory $factory,
            /** @Named("assetic.filters") */$filters = []) {
        return new SpotAsseticExtension($factory, $filters);
    }
    
    /** @Provides(Provides::ELEMENT) @Named(WebApp::VIEW_RENDERERS) @Singleton */
    static function provideTwigAssetWriter(TwigAssetWriterRenderer $renderer) {
        return $renderer;
    }
}