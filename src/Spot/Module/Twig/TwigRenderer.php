<?php
namespace Spot\Module\Twig;

use Spot\Http\Response;
use Spot\App\Web\View;
use Spot\App\Web\ViewRenderer;

class TwigRenderer implements ViewRenderer {
    private $env;

    public function __construct(\Twig_Environment $env) {
        $this->env = $env;
    }

    public function render(View $view, Response $response) {
        $loader = $this->env->getLoader();
        $loader->addPath(dirname($view->getTemplate()));

        $content = $this->env->render(
            $view->getTemplate(),
            $view->getModel()
        );
        $response->setContent($content);
    }

    static public function rendererOf(View $view) {
        return $view instanceof TwigView;
    }
}