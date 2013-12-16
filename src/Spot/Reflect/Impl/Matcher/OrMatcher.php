<?php
namespace Spot\Reflect\Impl\Matcher;

use Spot\Reflect\Matcher;
use Spot\Reflect\Type;

class OrMatcher extends Matcher {
    public $o1, $o2;

    public function __construct(Matcher $o1, Matcher $o2) {
        $this->o1 = $o1;
        $this->o2 = $o2;
    }

    public function matches(Type $type) {
        return $this->o1->matches($type) || $this->o2->matches($type);
    }
}
