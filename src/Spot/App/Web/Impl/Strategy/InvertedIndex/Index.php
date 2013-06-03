<?php
namespace Spot\App\Web\Impl\Strategy\InvertedIndex;

use Spot\Http\Request;

interface Index {
    function matches(Request $request, array $filtered = []);
}