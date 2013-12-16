<?php
namespace Spot\Module\Twig;

use Spot\App\Web\Request;
use Spot\App\Web\Response;
use Spot\App\Web\View;
use Spot\App\Web\ViewRenderer;

class TwigRenderer implements ViewRenderer {
    private $env;

    public function __construct(\Twig_Environment $env) {
        $this->env = $env;
    }

    function render(View $view, Request $request, Response $response) {
        $rendered = $this->env->render($view->getTemplate(), $view->getModel());

        $response->write($rendered);
    }

    static function rendererOf(View $view) {
        return $view instanceof TwigView;
    }
}
