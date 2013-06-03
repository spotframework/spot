<?php
namespace Spot\App\Cli\Impl;

use Spot\App\Cli\Args;

class RoutingStrategy {
    public function resolve(Args $args) {
        foreach(static::$actions as $action) {
            if($action::matches($args)) {
                return $action;
            }
        }
    }
}