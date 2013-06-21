<?php
namespace Spot\Module\Aws\S3\Impl;

use Spot\Http\Request\File;
use Spot\Module\Aws\S3\S3Uploader;

class S3TransactionalUploader extends S3Uploader {
    private $uploads;
    
    public function __construct(S3UploadList $uploads) {
        $this->uploads = $uploads;
    }
    
    public function upload(File $file, $destination) {
        $this->uploads->add($file, $destination);
    }
}