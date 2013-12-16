<?php
namespace Spot\Reflect\Impl\Matcher;

use Spot\Reflect\Matcher;
use Spot\Reflect\Type;

class NotMatcher extends Matcher {
    public $m;

    public function __construct(Matcher $m) {
        $this->m = $m;
    }

    public function matches(Type $type) {
        return !$this->m->matches($type);
    }
}
