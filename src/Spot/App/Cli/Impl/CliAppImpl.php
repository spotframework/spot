<?php
namespace Spot\App\Cli\Impl;

use Spot\App\Input;
use Spot\App\Cli\CliApp;

class CliAppImpl implements CliApp {
    private $router;

    public function __construct(Router $router) {
        $this->router = $router;
    }

    public function handle(Input $args) {
        $this->router->resolve($args);
    }
}