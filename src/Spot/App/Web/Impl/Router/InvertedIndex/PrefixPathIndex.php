<?php
namespace Spot\App\Web\Impl\Router\InvertedIndex;

use Spot\App\Web\Request;

class PrefixPathIndex {
    private $indexes;

    public function __construct(array $indexes) {
        $this->indexes = $indexes;
    }

    public function matches(Request $request) {
        $uri = $request->uri();

        $indexes = [];
        foreach($this->indexes as $prefix => $index) {
            if(strpos($uri, $prefix) === 0) {
                $indexes += $index;
            }
        }

        return $indexes;
    }
}
