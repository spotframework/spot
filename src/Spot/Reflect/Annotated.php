<?php
namespace Spot\Reflect;

interface Annotated {
    function isAnnotatedWith($annotation);

    function getAnnotations($annotationName = null);

    function getAnnotation($annotationName);
}
