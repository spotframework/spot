<?php
namespace Spot\App\Web\Impl\Strategy\InvertedIndex;

use Spot\Http\Request;

abstract class ParamsIndex implements Index {
    public function matches(Request $request, array $filtered = []) {
        $index = [];
        foreach(static::$index as $i => $params) {
            foreach($params as $key => $value) {
                if( isset($request[$key])
                    &&
                    ($value === null || $request[$key] === $value)
                    ) {
                    $index[$i] = 1;
                }
            }
        }

        return $index;
    }
}