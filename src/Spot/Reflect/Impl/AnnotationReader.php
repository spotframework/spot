<?php
namespace Spot\Reflect\Impl;

use Doctrine\Common\Annotations\Reader as DoctrineReader;
use Doctrine\Common\Annotations\DocParser;
use Doctrine\Common\Annotations\PhpParser;

class AnnotationReader {
    private static $globalImports = array(
        'ignoreannotation' => 'Doctrine\Common\Annotations\Annotation\IgnoreAnnotation',
    );
    
    private static $globalIgnoredNames = array(
        'access'=> true, 'author'=> true, 'copyright'=> true, 'deprecated'=> true,
        'example'=> true, 'ignore'=> true, 'internal'=> true, 'link'=> true, 'see'=> true,
        'since'=> true, 'tutorial'=> true, 'version'=> true, 'package'=> true,
        'subpackage'=> true, 'name'=> true, 'global'=> true, 'param'=> true,
        'return'=> true, 'staticvar'=> true, 'category'=> true, 'staticVar'=> true,
        'static'=> true, 'var'=> true, 'throws'=> true, 'inheritdoc'=> true,
        'inheritDoc'=> true, 'license'=> true, 'todo'=> true, 'deprecated'=> true,
        'deprec'=> true, 'author'=> true, 'property' => true, 'method' => true,
        'abstract'=> true, 'exception'=> true, 'magic' => true, 'api' => true,
        'final'=> true, 'filesource'=> true, 'throw' => true, 'uses' => true,
        'usedby'=> true, 'private' => true, 'Annotation' => true, 'override' => true,
        'Required' => true, 'codeCoverageIgnoreStart' => true, 'codeCoverageIgnoreEnd' => true,
        'Target' => true, 'Attribute' => true, 'Attributes' => true, 'Required' => true,
    );
    
    private $doctrine,
            $parser,
            $preParser,
            $phpParser;
    
    public function __construct(DoctrineReader $doctrine) {
        $this->doctrine = $doctrine;
        $this->parser = new DocParser();
        $this->preParser = new DocParser();
        $this->preParser->setImports(self::$globalImports);
        $this->preParser->setIgnoreNotImportedAnnotations(true);
        
        $this->phpParser = new PhpParser();
    }
    
    public function getClassAnnotations(\ReflectionClass $refl) {
        return $this->doctrine->getClassAnnotations($refl);
    }
    
    public function getMethodAnnotations(\ReflectionMethod $refl) {
        return $this->doctrine->getMethodAnnotations($refl);
    }
    
    public function getPropertyAnnotations(\ReflectionProperty $refl) {
        return $this->doctrine->getPropertyAnnotations($refl);
    }
    
    private function getImports(\ReflectionClass $class)
    {
        if (isset($this->imports[$name = $class->getName()])) {
            return $this->imports[$name];
        }
        
        $this->collectParsingMetadata($class);

        return $this->imports[$name];
    }
    
    private function collectParsingMetadata(\ReflectionClass $class)
    {
        $ignoredAnnotationNames = self::$globalIgnoredNames;

        $annotations = $this->preParser->parse($class->getDocComment(), 'class '.$class->name);
        foreach ($annotations as $annotation) {
            if ($annotation instanceof IgnoreAnnotation) {
                foreach ($annotation->names AS $annot) {
                    $ignoredAnnotationNames[$annot] = true;
                }
            }
        }

        $name = $class->getName();
        $this->imports[$name] = array_merge(
            self::$globalImports,
            $this->phpParser->parseClass($class),
            array('__NAMESPACE__' => $class->getNamespaceName())
        );
        $this->ignoredAnnotationNames[$name] = $ignoredAnnotationNames;
    }
    
    private function getIgnoredAnnotationNames(\ReflectionClass $class)
    {
        if (isset($this->ignoredAnnotationNames[$name = $class->getName()])) {
            return $this->ignoredAnnotationNames[$name];
        }
        $this->collectParsingMetadata($class);

        return $this->ignoredAnnotationNames[$name];
    }
    
    public function getParameterAnnotations(\ReflectionParameter $parameter) {
        $class = $parameter->getDeclaringClass() ?: 'Closure';
        $method = $parameter->getDeclaringFunction();
        
        if (!$method->isUserDefined()) {
            return array();
        }
        
        $context = 'parameter ' . ($class === 'Closure' ? $class : $class->getName()) . '::' . $method->getName() . '($'.$parameter->getName().')';
        if ($class === 'Closure') {
            $this->parser->setImports($this->getClosureImports($method));
            //$this->parser->setIgnoredAnnotationNames($this->getIgnoredAnnotationNames(new ReflectionClass('Closure')));
        } else {
            $this->parser->setImports($this->getImports($class));
            $this->parser->setIgnoredAnnotationNames($this->getIgnoredAnnotationNames($class));
        }
       
        $lines = file($method->getFileName());
        $lines = array_slice($lines, $start = $method->getStartLine() - 1, $method->getEndLine() - $start);
        $methodBody = Implode($lines);

        $methodBody = str_replace("\n", null, $methodBody);
        $signature = preg_split('/\)\s*\{/', $methodBody);
        $signature = $signature[0];
        $signature = substr($signature, strpos($signature, 'function'));
        
        if (preg_match_all('/\/\*\*(.*?)\*\/'.'.*?\$(\w+)/', $signature, $matches)) {
            $docComments = $matches[1];
            $names = $matches[2];
            
            for ($i = 0, $len = count($names); $i < $len; ++$i) {
                if ($names[$i] === $parameter->name) {
                    return $this->parser->parse($docComments[$i], $context);
                }
            }
        }

        return array();
    }
}