<?php
namespace Spot\Module\Aws\S3;

use Aws\S3\S3Client;
use Spot\Http\Request\File;

class S3Uploader {
    private $s3,
            $bucket;
    
    public function __construct(S3Client $s3, $bucket) {
        $this->s3 = $s3;
        $this->bucket = $bucket;
    }
    
    public function upload(File $file, $destination) {
        $result = $this->s3->putObject([
            "ACL" => "public-read",
            "Bucket" => $this->bucket,
            "Key" => $destination,
            "Body" => fopen($file->tmp_name, "rb"),
        ]);
        
        return $result["ObjectUrl"];
    }
}