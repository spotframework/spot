<?php
namespace Spot\Domain;

use Spot\Inject\Qualifier;

/** @Annotation @Target({"CLASS", "METHOD"}) */
final class Transactional implements Qualifier {
    function __toString() {
        return "@".__CLASS__;
    }
}
