<?php
namespace Spot\Module\Twig;

use Spot\App\Web\View;

class TwigView implements View {
    private $model,
            $template;

    public function __construct($template, array $model = []) {
        $this->model = $model;
        $this->template = $template;
    }

    public function getModel() {
        return $this->model;
    }

    public function getTemplate() {
        return $this->template;
    }
}
