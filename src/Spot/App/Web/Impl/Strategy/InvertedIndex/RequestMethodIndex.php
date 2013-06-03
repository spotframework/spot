<?php
namespace Spot\App\Web\Impl\Strategy\InvertedIndex;

use Spot\Http\Request;

abstract class RequestMethodIndex implements Index {
    public function matches(Request $request, array $filtered = []) {
        return isset(static::$index[$method = $request->getMethod()])
            ? static::$index[$method]
            : [];
    }
}