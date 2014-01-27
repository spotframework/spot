<?php
namespace Spot\Domain;

use Spot\Inject\Qualifier;

/** @Annotation */
final class Transactional implements Qualifier {
    function __toString() {
        return "@".__CLASS__;
    }
}
