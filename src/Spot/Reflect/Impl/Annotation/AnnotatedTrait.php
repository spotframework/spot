<?php
namespace Spot\Reflect\Impl\Annotation;


trait AnnotatedTrait {
    private $reflection,
        $annotations;

    public function getAnnotations($annotationName = null) {
        if(empty($this->annotations)) {
            $this->annotations = $this->reflection->getAnnotations($this);
        }

        if(empty($annotationName)) {
            return $this->annotations;
        }

        $annotations = [];
        foreach($this->annotations as $annotation) {
            if($annotation instanceof $annotationName) {
                $annotations[] = $annotation;
            }
        }

        return $annotations;
    }

    public function getAnnotation($annotationName) {
        foreach($this->getAnnotations() as $annotation) {
            if($annotation instanceof $annotationName) {
                return $annotation;
            }
        }
    }

    public function isAnnotatedWith($annotation) {
        if(is_object($annotation)) {
            foreach($this->getAnnotations() as $annot) {
                if($annotation == $annot) {
                    return true;
                }
            }
        } else {
            return (bool)$this->getAnnotation($annotation);
        }

        return false;
    }
}

