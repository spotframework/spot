<?php
namespace Spot\Reflect;

interface Matcher {
    /**
     * @param \Spot\Reflect\Type $type
     * 
     * @return boolean
     */
    function match(Type $type);
}