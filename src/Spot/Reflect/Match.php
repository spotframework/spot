<?php
namespace Spot\Reflect;

abstract class AbstractMatcher implements Matcher {
    public function andIt(Matcher $matcher) {
        return new AndMatcher($this, $matcher);
    }
    
    public function orIt(Matcher $matcher) {
        return new OrMatcher($this, $matcher);
    }
}

class AndMatcher extends AbstractMatcher {
    private $first,
            $second;
    
    public function __construct(Matcher $first, Matcher $second) {
        $this->first = $first;
        $this->second = $second;
    }
    
    public function match(Type $type) {
        return $this->first->match($type) and $this->second->match($type);
    }    
}

class OrMatcher extends AbstractMatcher {
    private $first,
            $second;
    
    public function __construct(Matcher $first, Matcher $second) {
        $this->first = $first;
        $this->second = $second;
    }
    
    public function match(Type $type) {
        return $this->first->match($type) or $this->second->match($type);
    }
}

class Any extends AbstractMatcher {
    public function match(Type $type) {
        return true;
    }
}

class Only extends AbstractMatcher {
    private $typeName;
    
    public function __construct($typeName) {
        $this->typeName = $typeName;
    }
    
    public function match(Type $type) {
        return $this->typeName === $type->name;
    }
}

class AnnotatedWith extends AbstractMatcher {
    private $annotation;
    
    public function __construct($annotation) {
        $this->annotation = $annotation;
    }

    public function match(Type $type) {
        return $type->isAnnotatedWith($this->annotation);
    }
}

class SubTypeOf extends AbstractMatcher {
    private $super;
    
    public function __construct($super) {
        $this->super = $super;
    }
    
    public function match(Type $type) {
        return $type->isSubTypeOf($this->super);
    }
}

class HasConstructor extends AbstractMatcher {
    public function match(Type $type) {
        return (bool)$type->getConstructor();
    }
}

class HasMethod extends AbstractMatcher {
    public $modifiers;

    public function __construct($modifiers) {
        $this->modifiers = $modifiers;
    }

    public function match(Type $type) {
        return (bool)($this->modifiers
            ? $type->getMethods($this->modifiers)
            : $type->getMethods());
    }
}

class HasMethodAnnotatedWith extends AbstractMatcher {
    private $annotation;
    
    public function __construct($annotation) {
        $this->annotation = $annotation;
    }
    
    public function match(Type $type) {
        foreach($type->getMethods() as $method) {
            if($method->isAnnotatedWith($this->annotation)) {
                return true;
            }
        }
        
        return false;
    }    
}

class HasParameterAnnotatedWith extends AbstractMatcher {
    private $annotation;
    
    public function __construct($annotation) {
        $this->annotation = $annotation;
    }
    
    public function match(Type $type) {
        foreach($type->getMethods() as $method) {
            foreach($method->getParameters() as $parameter) {
                if($parameter->isAnnotatedWith($this->annotation)) {
                    return true;
                }
            }
        }
        
        return false;
    }
}

class WithModifiers extends AbstractMatcher {
    private $modifiers;
    
    public function __construct($modifiers) {
        $this->modifiers = $modifiers;
    }
    
    public function match(Type $type) {
        return $type->getModifiers() & $this->modifiers;
    }    
}

class InterfaceOnly extends AbstractMatcher {
    public function match(Type $type) {
        return $type->isInterface();
    }    
}

class AbstractOnly extends AbstractMatcher {
    public function match(Type $type) {
        return $type->isAbstract();
    }    
}

class InstantiableOnly extends AbstractMatcher {
    public function match(Type $type) {
        return $type->isInstantiable();
    }    
}

class Contains extends AbstractMatcher {
    private $substring;

    public function __construct($substring) {
        $this->substring = strtolower($substring);
    }

    public function match(Type $type) {
        return strpos(strtolower($type->name), $this->substring) !== false;
    }
}

final class Match {
    private function __construct() {}
    
    static public function any() {
        return new Any();
    }
    
    static public function only($typeName) {
        return new Only($typeName);
    }
    
    static public function annotatedWith($annotation) {
        return new AnnotatedWith($annotation);
    }
    
    static public function subTypeOf($super) {
        return new SubTypeOf($super);
    }
    
    static public function withModifiers($modifiers) {
        return new WithModifiers($modifiers);
    }
    
    static public function abstractOnly() {
        return new AbstractOnly();
    }
    
    static public function interfaceOnly() {
        return new InterfaceOnly();
    }
    
    static public function finalOnly() {
        return self::withModifiers(Type::IS_FINAL);
    }
    
    static public function instantiableOnly() {
        return new InstantiableOnly();
    }

    static public function contains($substring) {
        return new Contains($substring);
    }

    static public function hasMethod($modifiers = null) {
        return new HasMethod($modifiers);
    }
    
    static public function hasMethodAnnotatedWith($annotation) {
        return new HasMethodAnnotatedWith($annotation);
    }
    
    static public function hasParameterAnnotatedWith($annotation) {
        return new HasParameterAnnotatedWith($annotation);
    }
}