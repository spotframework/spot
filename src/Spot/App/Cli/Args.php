<?php
namespace Spot\App\Cli;

use Spot\App\Input;
use ArrayObject;
use ArrayIterator;

class Args extends ArrayObject implements Input {
    public function __construct(array $args) {
        parent::__construct($args);
    }

    static function createFromGlobal() {
        $argv = $GLOBALS["argv"];
        array_shift($argv);

        $args = [];
        for($i = 0, $c = count($argv); $i < $c; ++$i) {
            $arg = $argv[$i];
            if(isset($arg[0]) && $arg[0] === "-") {
                $args[$arg] = [];
                while(isset($argv[++$i]) && $argv[$i][0] !== "-") {
                    $args[$arg][] =  $argv[$i++];
                }
            }
        }

        return self::create($args);
    }

    static function create(array $args) {
        return new self($args);
    }
}