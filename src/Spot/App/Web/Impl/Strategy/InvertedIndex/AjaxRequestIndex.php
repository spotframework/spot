<?php
namespace Spot\App\Web\Impl\Strategy\InvertedIndex;

use Spot\Http\Request;

abstract class AjaxRequestIndex implements Index {
    public function matches(Request $request, array $filtered = []) {
        return static::$index[$request->isAjax()];
    }
}