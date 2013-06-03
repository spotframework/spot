<?php
namespace Spot\App\Rest\Impl;

use Spot\Inject\Named;
use Spot\Reflect\Match;
use Spot\Reflect\Reflection;

class ResourceScanner {
    private $reflection,
            $namespaces;

    public function __construct(
            Reflection $reflection,
            /** @Named("app.module.namespaces") */array $namespaces) {
        $this->reflection = $reflection;
        $this->namespaces = $namespaces;
    }

    public function scan() {
        $resources = [];
        $matcher = Match::annotatedWith("Spot\App\Rest\Resource");
        foreach($this->namespaces as $ns) {
            $resources = array_merge(
                $resources,
                $this->reflection->find($ns, $matcher)
            );
        }
        return $resources;
    }
}