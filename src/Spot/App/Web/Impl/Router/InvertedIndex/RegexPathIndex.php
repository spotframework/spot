<?php
namespace Spot\App\Web\Impl\Router\InvertedIndex;

use Spot\App\Web\Request;

class RegexPathIndex {
    private $indexes;

    public function __construct(array $indexes) {
        $this->indexes = $indexes;
    }

    public function matches(Request $request, array $filtered) {
        $uri = $request->uri();
        foreach(array_intersect_key($this->indexes, $filtered) as $i => $pattern) {
            if(preg_match($pattern, $uri, $paths)) {
                foreach($paths as $name => $value) {
                    is_string($name) && !isset($request[$name]) &&
                        $request[$name] = $value;
                }

                return [$i => 1];
            }
        }

        return [];
    }
}
