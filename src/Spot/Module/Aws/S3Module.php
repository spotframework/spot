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
            /** @Named("amazon.s3.key") */$key,
            /** @Named("amazon.s3.secret") */$secret,
            /** @Named("amazon.s3.token") */$token = null) {
        return S3Client::factory([
            "key" => $key,
            "secret" => $secret,
            "token" => $token,
        ]);
    }
}