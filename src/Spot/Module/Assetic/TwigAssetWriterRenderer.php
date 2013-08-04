<?php
namespace Spot\Module\Assetic;

use Spot\App\Web\View;
use Spot\Inject\Named;
use Spot\Http\Request;
use Spot\Http\Response;
use Spot\App\Web\ViewRenderer;
use Spot\Module\Twig\TwigView;
use Assetic\Factory\AssetFactory;

use Assetic\AssetWriter;
use Assetic\Extension\Twig\TwigFormulaLoader;
use Assetic\Extension\Twig\TwigResource;
use Assetic\Factory\LazyAssetManager;

class TwigAssetWriterRenderer implements ViewRenderer {
    private $path,
            $am,
            $twigLoader;
    
    public function __construct(
            /** @Named("assetic.output") */$path,
            AssetFactory $factory,
            \Twig_Environment $twig,
            \Twig_LoaderInterface $twigLoader) {
        $this->path = realpath($path);
        $this->am = new LazyAssetManager($factory);
        $this->am->setLoader("twig", new TwigFormulaLoader($twig));
        $this->twigLoader = $twigLoader;
    }
    
    public function render(View $view, Request $request, Response $response) {
        $resource = new TwigResource($this->twigLoader, $view->getTemplate());
        $this->am->addResource($resource, "twig");

        $writer = new AssetWriter($this->path);
        $writer->writeManagerAssets($this->am);
    }

    public static function rendererOf(View $view) {
        return $view instanceof TwigView;
    }    
}