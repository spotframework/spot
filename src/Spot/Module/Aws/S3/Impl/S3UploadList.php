<?php
namespace Spot\Module\Aws\S3\Impl;

use ArrayIterator;
use IteratorAggregate;
use Spot\Http\Request\File;

class S3UploadList implements IteratorAggregate {
    private $uploads = [];
    
    public function add(File $file, $destination) {
        $this->uploads[] = ["file" => $file, "destination" => $destination];
    }
    
    public function clear() {
        $this->uploads = [];
    }
    
    public function getIterator() {
        return new ArrayIterator($this->uploads);
    }
}