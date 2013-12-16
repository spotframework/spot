<?php
namespace Spot\Reflect;

interface Reflection {
    /**
     * @param string $type
     * @return Type
     */
    function get($type);

    /**
     * @param string $namespace
     * @param Matcher $matcher
     * @return Type[]
     */
    function find($namespace, Matcher $matcher);

    function getAnnotations(Annotated $annotated);
}
