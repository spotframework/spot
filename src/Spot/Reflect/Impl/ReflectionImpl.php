<?php
namespace Spot\Reflect\Impl;

use Spot\Reflect\Reflection;
use Spot\Reflect\Type;
use Spot\Reflect\Method;
use Spot\Reflect\Parameter;
use Spot\Reflect\Matcher;
use Spot\Reflect\Annotated;
use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\AnnotationReader as DoctrineReader;

class ReflectionImpl implements Reflection {
    private $annotationReader,
            $nsLoader;
    
    public function __construct(
            AnnotationReader $annotationReader,
            NamespaceLoader $nsLoader) {
        $this->annotationReader = $annotationReader;
        $this->nsLoader = $nsLoader;
    }
    
    public function find($namespace, Matcher $matcher) {
        $this->nsLoader->load($namespace);        
        $declaredTypes = array_merge(
            get_declared_classes(),
            get_declared_interfaces()
        );
        
        $matchedTypes = [];
        foreach($declaredTypes as $typeName) {
            if(strpos($typeName, $namespace) === 0) {
                $type = $this->getType($typeName);
                if($matcher->match($type)) {
                    $matchedTypes[] = $type;
                }
            }
        }
        
        return $matchedTypes;
    }

    public function getAnnotations(Annotated $annotated) {
        if($annotated instanceof \ReflectionClass) {
            return $this->annotationReader->getClassAnnotations($annotated);
        }
        
        if($annotated instanceof \ReflectionMethod) {
            return $this->annotationReader->getMethodAnnotations($annotated);
        }
        
        if($annotated instanceof \ReflectionParameter) {
            return $this->annotationReader->getParameterAnnotations($annotated);
        }
        
        if($annotated instanceof \ReflectionProperty) {
            return $this->annotationReader->getPropertyAnnotations($annotated);
        }
    }

    public function getMethod(Type $type, $name) {
        return new Method($type, $name, $this);
    }

    public function getParameter(Method $method, $name) {
        return new Parameter($method, $name, $this);
    }

    public function getType($name) {
        return new Type($name, $this);
    }    
    
    static public function create(Cache $cache) {
        foreach(spl_autoload_functions() as $callable) {
            AnnotationRegistry::registerLoader($callable);
        }
        
        $doctrineReader = new DoctrineReader();
        $doctrineReader = new CachedReader($doctrineReader, $cache);
        $reader = new AnnotationReader($doctrineReader);
        $phpLoader = new PhpLoader();
        $nsLoader = new NamespaceLoader($phpLoader);
        
        return new self($reader, $nsLoader);
    }
}