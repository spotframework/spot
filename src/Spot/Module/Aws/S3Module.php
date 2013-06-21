<?php
namespace Spot\Module\Aws;

use Aws\S3\S3Client;
use Spot\Inject\Named;
use Spot\Inject\Provides;
use Spot\Inject\Singleton;
use Spot\Domain\Transactional;
use Spot\Module\Aws\S3\S3Uploader;
use Spot\Module\Aws\S3\Impl\S3UploadList;
use Spot\Module\Aws\S3\Impl\S3UnitOfWork;
use Spot\Module\Aws\S3\Impl\S3TransactionalUploader;
class S3Module {
    /** @Provides("Aws\S3\S3Client") @Singleton */
    static function provideS3(
            /** @Named("amazon.key") */$key,
            /** @Named("amazon.secret") */$secret,
            /** @Named("amazon.token") */$token = null) {
        return S3Client::factory([
            "key" => $key,
            "secret" => $secret,
            "token" => $token,
        ]);
    }
    
    /** @Provides("Spot\Module\Aws\S3\Impl\S3UploadList") @Singleton */
    static function provideUploadList() {
        return new S3UploadList();
    }
    
    /** @Provides("Spot\Module\Aws\S3\S3Uploader") */
    static function provideUploader(
            S3Client $s3,
            /** @Named("amazon.s3.bucket-name") */$bucket) {
        return new S3Uploader($s3, $bucket);
    }
    
    /** @Provides("Spot\Module\Aws\S3\S3Uploader") @Transactional */
    public function provideTransactionalUploader(S3TransactionalUploader $uploader) {
        return $uploader;
    }
    
    /** @Provides(Provides::ELEMENT) @Transactional */
    public function provideUploaderUnitOfWork(S3UnitOfWork $work) {
        return $work;
    }
}