<?php
namespace Spot\Reflect;

interface Reflection {
    /**
     * @return \Spot\Reflect\Type
     */
    function getType($name);
    
    /**
     * @return \Spot\Reflect\Method
     */
    function getMethod(Type $type, $name);
    
    /**
     * @return \Spot\Reflect\Parameter
     */
    function getParameter(Method $method, $name);
    
    /**
     * @return array 
     */
    function getAnnotations(Annotated $annotated);
    
    /**
     * Find types in given namespace that matches given matcher criteria
     * 
     * @param string $namespace
     * @param Matcher $matcher
     * 
     * @return array
     */
    function find($namespace, Matcher $matcher);
}