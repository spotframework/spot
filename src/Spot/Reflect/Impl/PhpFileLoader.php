<?php
namespace Spot\Reflect\Impl;

class PhpFileLoader {
    public function load($path) {
        $i = new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS);
        $i = new \RecursiveIteratorIterator($i);
        $i = new \CallbackFilterIterator($i, [$this, "isValid"]);

        foreach($i as $file) {
            require_once $file;
        }
    }

    public function isValid(\SplFileInfo $file) {
        return $file->getExtension() == "php";
    }
}
