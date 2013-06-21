<?php
namespace Spot\Module\Aws\S3\Impl;

use Spot\Domain\UnitOfWork;
use Spot\Module\Aws\S3\S3Uploader;

class S3UnitOfWork implements UnitOfWork {
    private $uploads,
            $uploader;
    
    public function __construct(S3UploadList $uploads, S3Uploader $uploader) {
        $this->uploads = $uploads;
        $this->uploader = $uploader;
    }
    
    public function commit() {
        foreach($this->uploads as $upload) {
            $this->uploader->upload($upload["file"], $upload["destination"]);
        }
    }

    public function rollback() {
        $this->uploads->clear();
    }    
}