<?php
namespace Spot\Inject\Impl;

use ArrayIterator;
use IteratorAggregate;

class Modules extends \ArrayObject {
    private $hash;
    
    public function hash() {
        if(empty($this->hash)) {
            $modules = [];
            foreach($this as $module) {
                if(is_object($module)) {
                    $module = get_class($module);
                }
            }
            
            $this->hash = md5(implode($modules));
        }
        
        return $this->hash;
    }
}
