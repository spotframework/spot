<?php
namespace Spot\App\Web\Impl;

/**
 * Compile route path that contains wildcard / placeholder
 * into appropriate regex pattern
 */
class RoutePathCompiler {
    public function compile($path) {
        static
            $patterns = [
                0 => '/\*/',
                1 => '/\{\s*\$(\w[\d|\w|_|\-]*)\s*\}/',
            ],
            $replaces = [
                0 => '([\w|\-|_|\.]+)',
                1 => '(?P<$1>[\w|\-|_|\.]+)',
            ];

        return preg_replace($patterns, $replaces, $path);
    }
}