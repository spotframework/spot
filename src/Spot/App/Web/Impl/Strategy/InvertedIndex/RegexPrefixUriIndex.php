<?php
namespace Spot\App\Web\Impl\Strategy\InvertedIndex;

use Spot\Http\Request;

class RegexPrefixUriIndex implements Index {
    public function matches(Request $request, array $filtered = []) {
        $index = [];
        $uri = (string)$request->getUri();
        foreach(static::$index as $prefix => $i) {
            if(strpos($uri, $prefix) === 0) {
                $index += $i;
            }
        }

        return $index;
    }
}