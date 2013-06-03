<?php
namespace Spot\Reflect;

interface Annotated {
    /**
     * @param string $annotationName
     * 
     * @return object | null
     */
    function getAnnotation($annotationName);
    
    /**
     * @param type $annotationName
     * 
     * @return array
     */
    function getAnnotations($annotationName = null);
    
    /**
     * @param string | object $annotation
     * 
     * @return boolean
     */
    function isAnnotatedWith($annotation);
}