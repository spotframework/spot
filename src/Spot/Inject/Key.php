<?php
namespace Spot\Inject;

use Spot\Reflect\Method;
use Spot\Reflect\Parameter;

final class Key {
    private $t, $q, $h;

    protected function __construct($t, Qualifier $qualifier = null) {
        $this->t = $t;
        $this->q = $qualifier;
    }

    public function getQualifier() {
        return $this->q;
    }

    public function getType() {
        return $this->t;
    }

    public function hash() {
        return $this->h ?: $this->h = md5(serialize($this));
    }

    public function isCollection() {
        return $this->getType() === Provides::ELEMENT;
    }

    public function isConstant() {
        return $this->getType() === Provides::CONSTANT;
    }

    public function isType() {
        return !$this->isCollection() && !$this->isConstant();
    }

    public function __toString() {
        return $this->t.($this->q ? " ".$this->q : $this->q);
    }

    static public function ofType($type, Qualifier $qualifier = null) {
        return new Key($type, $qualifier);
    }

    static public function ofCollection(Qualifier $qualifier) {
        return new Key(Provides::ELEMENT, $qualifier);
    }

    static public function ofConstant(Qualifier $qualifier) {
        return new Key(Provides::CONSTANT, $qualifier);
    }

    static public function ofProvider(Method $method) {
        $provides = $method->getAnnotation("Spot\\Inject\\Provides");
        $qualifier = $method->getAnnotation("Spot\\Inject\\Qualifier");
        if( !$qualifier
            &&
            ($provides->value == Provides::ELEMENT || $provides->value == Provides::CONSTANT)) {
            throw new ConfigurationException(
                "Invalid provider method in {$method->class}::{$method->name}".
                ", constant or element provider must be annotated with Qualifier annotation"
            );
        }

        return new Key($provides->value, $qualifier);
    }

    static public function ofParameter(Parameter $parameter) {
        $qualifier = $parameter->getAnnotation("Spot\\Inject\\Qualifier");
        if(($class = $parameter->getClass())) {
            return Key::ofType($class->name, $qualifier);
        }

        if(!$qualifier) {
            throw new ConfigurationException(
                "Parameter \${$parameter->name} in ".
                $parameter->getDeclaringClass()->name."::".
                $parameter->getDeclaringFunction()->name." is unbindable ".
                "because it's not type-hinted nor annotated with Qualifier annotation"
            );
        }

        if($parameter->isArray()) {
            return Key::ofCollection($qualifier);
        }

        return Key::ofConstant($qualifier);
    }
}
