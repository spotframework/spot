<?php
namespace Spot\App\Web\Impl\Router;

use Doctrine\Common\Cache\Cache;

class RoutePathCompiler {
    const VAR_PATTERN = '/\{\s*\$(\w[\d|\w|_|\-]*)\s*\}/';

    private $cache;

    public function __construct(Cache $cache) {
        $this->cache = $cache;
    }

    public function checkSymbol($path) {
        return strpos($path, "*") || strpos($path, "(") || $this->parse($path);
    }

    public function parse($path) {
        return preg_match_all(self::VAR_PATTERN, $path, $matches)
            ? array_combine($matches[0], $matches[1])
            : [];
    }

    public function compile($path) {
        static
            $PATTERNS = [
                0 => '/\*/',
                1 => self::VAR_PATTERN,
            ],
            $REPLACES = [
                0 => '[\w|\-|_|\.]+',
                1 => '(?P<$1>[\w|\-|_|\.]+)',
            ];

        return "/^".addcslashes(preg_replace($PATTERNS, $REPLACES, $path), "/-")."$/";
    }
}
