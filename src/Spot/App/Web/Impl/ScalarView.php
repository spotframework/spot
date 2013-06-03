<?php
namespace Spot\App\Web\Impl;

use Spot\App\Web\View;

class ScalarView implements View {
    private $value;

    public function __construct($value) {
        $this->value = $value;
    }

    public function getModel() {
        return $this->value;
    }
}