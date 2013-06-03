<?php
namespace Spot\App\Cli\Impl;

use Spot\Reflect\Method;

class MappingProvider {
    private $scanner;

    public function __construct(
            CommandScanner $scanner) {
        $this->scanner = $scanner;
    }

    public function get() {
        $actions = [];
        foreach($this->scanner->scan() as $command) {
            foreach($command->getMethods(Method::IS_PUBLIC) as $action) {
                var_dump($action);
            }
        }

        return $actions;
    }
}