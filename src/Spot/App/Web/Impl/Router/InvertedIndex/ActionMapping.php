<?php
namespace Spot\App\Web\Impl\Router\InvertedIndex;

use Spot\App\Web\Route;

class ActionMapping {
    public $route;

    public $action;

    public function __construct(Route $route, $action) {
        $this->route = $route;
        $this->action = $action;
    }
}
