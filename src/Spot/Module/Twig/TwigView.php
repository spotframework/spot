<?php
namespace Spot\Module\Twig;

use Spot\App\Web\View;

class TwigView implements View {
    private $template,
            $model;

    public function __construct($template, array $model = []) {
        $this->template = $template;
        $this->model = $model;
    }

    public function getModel() {
        return $this->model;
    }

    public function getTemplate() {
        return $this->template;
    }
}