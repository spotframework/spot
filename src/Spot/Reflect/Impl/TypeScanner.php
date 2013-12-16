<?php
namespace Spot\Reflect\Impl;

use Composer\Autoload\ClassLoader as ComposerLoader;

class TypeScanner {
    private $phpLoader;

    public function __construct(PhpFileLoader $phpLoader) {
        $this->phpLoader = $phpLoader;
    }

    public function scan($namespace) {
        $namespace = trim($namespace, "\\")."\\";

        $paths = [];
        foreach(spl_autoload_functions() as $loader) {
            if(is_array($loader) && count($loader) === 2) {
                if($loader[0] instanceof ComposerLoader) {
                    foreach($loader[0]->getPrefixes() as $prefix => $loaderPaths) {
                        if(strpos($prefix."\\", $namespace) === 0) {
                            $paths = array_merge($paths, $loaderPaths);
                        } else if(strpos($namespace, $prefix."\\") === 0) {
                            $paths = array_merge($paths, array_map(function ($path) use ($namespace, $prefix) {
                                return $path."/".str_replace("\\", "/", $namespace);
                            }, $loaderPaths));
                        }
                    }
                }
            }
        }

        array_map([$this->phpLoader, "load"], $paths);

        return array_filter(
            array_merge(
                get_declared_classes(),
                get_declared_interfaces()
            ),
            function($name) use ($namespace) {
                return stripos($name, $namespace) === 0;
            }
        );
    }
}
