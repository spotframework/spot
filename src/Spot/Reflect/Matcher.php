<?php
namespace Spot\Reflect;

use Spot\Reflect\Impl\Matcher\AndMatcher;
use Spot\Reflect\Impl\Matcher\NotMatcher;

abstract class Matcher {
    public function andIt(self $matcher) {
        return new AndMatcher($this, $matcher);
    }

    public function orIt(self $matcher) {
        return new OrMatcher($this, $matcher);
    }

    public function andNot(self $matcher) {
        return $this->andIt(new NotMatcher($matcher));
    }

    public function orNot(self $matcher) {
        return $this->orIt(new NotMatcher($matcher));
    }

    public abstract function matches(Type $type);
}
