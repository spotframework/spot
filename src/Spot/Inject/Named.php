<?php
namespace Spot\Inject;

/** @Annotation */
class Named implements Qualifier {
    public $value;
    
    public function __toString() {
        $s = '@'.get_class($this).'(';
        if(($fields = get_object_vars($this))) {
            if(isset($fields['value'])) {
                $s .= var_export($fields['value'], true);

                unset($fields['value']);
            } else {
                unset($fields['value']);
                
                $s .= key($fields).'='.var_export(array_shift($fields), true);
            }

            foreach($fields as $name => $value) {
                $s .= ', '.$name.'='.var_export($value, true);
            }
        }
        
        return $s.')';
    }

    /**
     * @param string $name
     * @return Named
     */
    static public function name($name) {
        $self = new static();
        
        $self->value = $name;
        
        return $self;
    }
}