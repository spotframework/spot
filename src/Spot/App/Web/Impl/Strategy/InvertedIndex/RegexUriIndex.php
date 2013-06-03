<?php
namespace Spot\App\Web\Impl\Strategy\InvertedIndex;

use Spot\Http\Request;
use Spot\Http\Request\Path;

abstract class RegexUriIndex implements Index {
    public function matches(Request $request, array $filtered = []) {
        $uri = (string)$request->getUri();
        foreach(array_intersect_key(static::$index, $filtered) as $i => $pattern) {
            if(preg_match($pattern, $uri, $matches)) {
                $request->path = new Path($matches);

                return [$i => 1];
            }
        }

        return [];
    }
}