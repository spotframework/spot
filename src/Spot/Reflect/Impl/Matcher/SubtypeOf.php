<?php
namespace Spot\Reflect\Impl\Matcher;

use Spot\Reflect\Matcher;
use Spot\Reflect\Type;

class SubtypeOf extends Matcher {
    public $s;

    public function __construct($super) {
        $this->s = $super;
    }

    public function matches(Type $type) {
        return $type->isSubtypeOf($this->s);
    }
}
