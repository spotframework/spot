<?php
namespace Spot\Reflect\Impl;

use Composer\Autoload\ClassLoader as ComposerLoader;

class NamespaceLoader {
    private $phpLoader;
    
    public function __construct(PhpLoader $phpLoader) {
        $this->phpLoader = $phpLoader;
    }

    public function load($ns) {
        $paths = [];
        foreach(spl_autoload_functions() as $loader) {
            if(is_array($loader) && count($loader) === 2) {
                if($loader[0] instanceof ComposerLoader) {
                    foreach($loader[0]->getPrefixes() as $prefix => $loaderPaths) {
                        if(strpos($prefix.'\\', $ns) === 0) {
                            $paths = array_merge($paths, $loaderPaths);
                        } else if(strpos($ns, $prefix.'\\') === 0) {
                            $paths = array_merge($paths, array_map(function ($path) use ($ns, $prefix) {
                                return $path.'/'.str_replace('\\', '/', $ns);
                            }, $loaderPaths));
                        }
                    }
                }
            }
        }
        
        foreach($paths as $path) {
            $this->phpLoader->load($path);
        }
    }
}