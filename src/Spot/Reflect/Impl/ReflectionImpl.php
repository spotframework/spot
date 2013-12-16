<?php
namespace Spot\Reflect\Impl;

use Spot\Reflect\Annotated;
use Spot\Reflect\Func;
use Spot\Reflect\Impl\Annotation\CachedReader;
use Spot\Reflect\Impl\Annotation\Reader;
use Spot\Reflect\Impl\Annotation\ReaderImpl;
use Spot\Reflect\Matcher;
use Spot\Reflect\Reflection;
use Spot\Reflect\Type;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Cache\Cache;

class ReflectionImpl implements Reflection {
    private $cache,
            $reader,
            $scanner,
            $types = [];

    protected function __construct(
            Cache $cache,
            Reader $reader,
            TypeScanner $scanner) {
        $this->cache = $cache;
        $this->reader = $reader;
        $this->scanner = $scanner;
    }

    function get($type) {
        return isset($this->types[$type])
            ? $this->types[$type]
            : $this->types[$type] = new Type($type, $this);
    }

    function find($namespace, Matcher $matcher) {
        $id = "#types#{$namespace}#".md5(serialize($matcher));
        if(($names = $this->cache->fetch($id)) !== false) {
            return array_map([$this, "get"], $names);
        }

        $names = $this->scanner->scan($namespace);
        $matches = array_filter(
            array_map([$this, "get"], $names),
            [$matcher, "matches"]
        );

        $this->cache->save($id, array_map(function($type) {
            return $type->name;
        }, $matches));

        return $matches;
    }

    function getAnnotations(Annotated $annotated) {
        if($annotated instanceof \ReflectionClass) {
            return $this->reader->getClassAnnotations($annotated);
        }

        if($annotated instanceof \ReflectionMethod) {
            return $this->reader->getMethodAnnotations($annotated);
        }

        if($annotated instanceof \ReflectionParameter) {
            return $this->reader->getParameterAnnotations($annotated);
        }

        if($annotated instanceof \ReflectionProperty) {
            return $this->reader->getPropertyAnnotations($annotated);
        }
    }


    static public function create(Cache $cache) {
        foreach(spl_autoload_functions() as $loader) {
            AnnotationRegistry::registerLoader($loader);
        }

        $reader = new ReaderImpl(new AnnotationReader());
        $reader = new CachedReader($cache, $reader);
        $loader = new PhpFileLoader();
        $scanner = new TypeScanner($loader);

        return new ReflectionImpl($cache, $reader, $scanner);
    }
}
