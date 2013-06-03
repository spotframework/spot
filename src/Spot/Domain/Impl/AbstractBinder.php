<?php
namespace Spot\Domain\Impl;

abstract class AbstractBinding {
    public $d;
    
    public function __construct(DomainManager $d) {
        $this->d = $d;
    }
}