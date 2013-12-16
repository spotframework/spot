<?php
namespace Spot\Reflect\Impl\Annotation;

interface Reader extends \Doctrine\Common\Annotations\Reader {
    function getParameterAnnotations(\ReflectionParameter $parameter);
}
