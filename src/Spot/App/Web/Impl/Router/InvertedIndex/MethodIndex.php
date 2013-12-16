<?php
namespace Spot\App\Web\Impl\Router\InvertedIndex;

use Spot\App\Web\Request;

class MethodIndex {
    private $indexes;

    public function __construct(array $indexes) {
        $this->indexes = $indexes;
    }

    public function matches(Request $request) {
        return isset($this->indexes[$request->method()])
            ? $this->indexes[$request->method()]
            : [];
    }
}
