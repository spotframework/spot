<?php
namespace Spot\Inject\Impl;

class Modules extends \ArrayObject {
    private $hash;
    
    public function hash() {
        if(empty($this->hash)) {
            $modules = [];
            foreach($this as $module) {
                if(is_object($module)) {
                    $module = get_class($module);
                }
                
                $modules[] = $module;
            }
            
            $this->hash = md5(implode($modules));
        }
        
        return $this->hash;
    }
    
    public function __toString() {
        return implode("\n *     ", array_map(function ($module) {
            if(is_object($module)) {
                return get_class($module);
            }
            
            return $module;
        }, iterator_to_array($this)));
    }
}
