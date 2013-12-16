<?php
namespace Spot\App\Web;

class ScalarView implements View {
    private $model;

    public function __construct($model) {
        $this->model = $model;
    }

    function getModel() {
        return $this->model;
    }
}
