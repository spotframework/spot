<?php
namespace Spot\App\Web\Impl\Strategy\InvertedIndex;

use Spot\Http\Request;

abstract class StaticUriIndex implements Index {
    public function matches(Request $request, array $filtered = []) {
        $uri = (string)$request->getUri();
        return isset(static::$index[$uri])
            ? static::$index[$uri]
            : [];
    }
}