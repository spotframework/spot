<?php
namespace Spot\Reflect\Impl\Annotation;

use Doctrine\Common\Annotations\CachedReader as DoctrineCachedReader;
use Doctrine\Common\Cache\Cache;

class CachedReader implements Reader {
    private $cache,
            $reader,
            $doctrine;

    public function __construct(
            Cache $cache,
            Reader $reader) {
        $this->cache = $cache;
        $this->reader = $reader;
        $this->doctrine = new DoctrineCachedReader($reader, $cache);
    }

    function getClassAnnotations(\ReflectionClass $class) {
        return $this->doctrine->getClassAnnotations($class);
    }

    function getClassAnnotation(\ReflectionClass $class, $annotationName) {
        return $this->doctrine->getClassAnnotation($class, $annotationName);
    }

    function getMethodAnnotations(\ReflectionMethod $method) {
        return $this->doctrine->getMethodAnnotations($method);
    }

    function getMethodAnnotation(\ReflectionMethod $method, $annotationName) {
        return $this->doctrine->getMethodAnnotation($method, $annotationName);
    }

    function getPropertyAnnotations(\ReflectionProperty $property) {
        return $this->getPropertyAnnotations($property);
    }

    function getPropertyAnnotation(\ReflectionProperty $property, $annotationName) {
        return $this->getPropertyAnnotation($property, $annotationName);
    }

    function getParameterAnnotations(\ReflectionParameter $parameter) {
        $key = "@[Annot]".$parameter->getDeclaringClass()."#".$parameter->getDeclaringFunction()."#".$parameter->name;
        if(($annotations = $this->cache->fetch($key)) === false) {
            $annotations = $this->reader->getParameterAnnotations($parameter);

            $this->cache->save($key, $annotations);
        }

        return $annotations;
    }
}
