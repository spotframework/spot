<?php
namespace Spot\Inject;

use Spot\Reflect\Method;
use Spot\Reflect\Parameter;

abstract class Key {
    private $q;
    
    protected function __construct(Qualifier $qualifier = null) {
        $this->q = $qualifier;
    }
    
    public function getQualifier() {
        return $this->q;
    }
    
    public function hash() {
        return md5(serialize($this));
    }
    
    public abstract function __toString();
    
    static public function ofType($type, Qualifier $qualifier = null) {
        return new TypeKey($type, $qualifier);
    }
    
    static public function ofConstant(Qualifier $qualifier) {
        return new ConstantKey($qualifier);
    }
    
    static public function ofElement(Qualifier $qualifier) {
        return new ElementKey($qualifier);
    }
    
    static public function ofParameter(Parameter $parameter) {
        $q = $parameter->getAnnotation('Spot\Inject\Qualifier');
        if(($class = $parameter->getClass())) {
            return self::ofType($class->name, $q);
        }
        
        if(empty($q)) {
            throw new \LogicException(
                'Parameter $'.$parameter->name.' in '.
                $parameter->getDeclaringClass()->name.'::'.
                $parameter->getDeclaringFunction()->name.' is unbindable '.
                'because it\'s not type-hinted nor annotated with Qualifier annotation'
            );
        }
        
        if($parameter->isArray()) {
            return self::ofElement($q);
        }
        
        return self::ofConstant($q);
    }
    
    static public function ofProvider(Method $method) {
        $type = $method->getAnnotation('Spot\Inject\Provides')->value;
        $q = $method->getAnnotation('Spot\Inject\Qualifier');
        if($type !== Provides::CONSTANT && $type !== Provides::ELEMENT) {
            return self::ofType($type, $q);
        }
        
        if(empty($q)) {
            throw new \LogicException(
                'Invalid provider method in '.$method->class.'::'.$method->name.
                ', constant or element provider must be annotated with Qualifier annotation'
            );
        }
        
        switch($type) {
            case Provides::ELEMENT:
                return self::ofElement($q);
            case Provides::CONSTANT:
                return self::ofConstant($q);
        }
    }
}

class TypeKey extends Key {
    private $t;
    
    protected function __construct($type, Qualifier $qualifier = null) {
        parent::__construct($qualifier);
        
        $this->t = $type;
    }
    
    public function getTypeName() {
        return $this->t;
    }
    
    public function __toString() {
        $s = "";
        if(($q = $this->getQualifier())) {
            $s = "$q ";
        }
        
        return $s.$this->getTypeName();
    }
}

class ConstantKey extends Key {
    public function __toString() {
        return $this->getQualifier()." ".Provides::CONSTANT;
    }
}

class ElementKey extends Key {
    public function __toString() {
        return $this->getQualifier()." ".Provides::ELEMENT;
    }
}