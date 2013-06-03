<?php
namespace Spot\App\Cli\Impl;

use Spot\Inject\Named;
use Spot\Reflect\Match;
use Spot\Reflect\Method;
use Spot\Reflect\Reflection;

class CommandScanner {
    private $reflection,
            $namespaces;

    public function __construct(
            Reflection $reflection,
            /** @Named("app.namespaces") */array $namespaces) {
        $this->reflection = $reflection;
        $this->namespaces = $namespaces;
    }

    public function scan() {
        $commands = [];
        $matcher =
            Match::annotatedWith("Spot\App\Cli\Command")->andIt(
            Match::hasMethod(Method::IS_PUBLIC));
        foreach($this->namespaces as $ns) {
            $commands[] = array_merge(
                $commands,
                $this->reflection->find($ns, $matcher)
            );
        }

        return $commands;
    }
}