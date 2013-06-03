<?php
namespace Spot\App\Web\Impl;

use Spot\Inject\Named;
use Spot\Reflect\Match;
use Spot\Reflect\Reflection;

class ControllerScanner {
    private $reflection,
            $namespaces;
    
    public function __construct(
            Reflection $reflection,
            /** @Named("app.module.namespaces") */array $namespaces) {
        $this->reflection = $reflection;
        $this->namespaces = $namespaces;
    }
    
    public function scan() {
        $controllers = [];
        $matcher = Match::annotatedWith("Spot\App\Web\Controller");
        foreach($this->namespaces as $ns) {
            $controllers = array_merge(
                $controllers,
                $this->reflection->find($ns, $matcher)
            );
        }
        
        return $controllers;
    }
}