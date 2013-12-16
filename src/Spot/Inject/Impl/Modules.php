<?php
namespace Spot\Inject\Impl;

class Modules extends \ArrayObject {
    private $hash;

    public function __construct(array $modules) {
        parent::__construct($modules);

        $this->hash = md5(implode(array_map(function ($module) {
            if(is_object($module)) {
                return get_class($module);
            }

            return $module;
        }, $modules)));
    }

    public function hash() {
        return $this->hash;
    }
}
