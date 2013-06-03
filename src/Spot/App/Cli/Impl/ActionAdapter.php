<?php
namespace Spot\App\Cli\Impl;

use Spot\App\Cli\Args;

abstract class ActionAdapter {
    public function execute(Args $args, ConsoleOutput $output) {

    }

    public abstract function matches(Args $args);
}