<?php
namespace Spot\Reflect\Impl\Matcher;

use Spot\Reflect\Matcher;
use Spot\Reflect\Type;

class InstantiableOnly extends Matcher {
    public function matches(Type $type) {
        return $type->isInstantiable();
    }
}
