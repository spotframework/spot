<?php
namespace Spot\Reflect;

use Spot\Reflect\Impl\Matcher\AnnotatedWith;
use Spot\Reflect\Impl\Matcher\Any;
use Spot\Reflect\Impl\Matcher\InstantiableOnly;
use Spot\Reflect\Impl\Matcher\Only;
use Spot\Reflect\Impl\Matcher\SubtypeOf;

final class Match {
    private function __construct() {}

    static public function any() {
        return new Any();
    }

    static public function only($type) {
        return new Only($type);
    }

    static public function annotatedWith($annotation) {
        return new AnnotatedWith($annotation);
    }

    static public function subtypeOf($super) {
        return new SubtypeOf($super);
    }

    static public function instantiableOnly() {
        return new InstantiableOnly();
    }
}
