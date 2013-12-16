<?php
namespace Spot\Reflect\Impl\Matcher;

use Spot\Reflect\Matcher;
use Spot\Reflect\Type;

class Only extends Matcher {
    public $t;

    public function __construct($type) {
        $this->t = $type;
    }

    public function matches(Type $type) {
        return $type->name === $this->t;
    }
}
