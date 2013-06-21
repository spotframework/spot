<?php
namespace Spot\Module\Aws;

use Aws\S3\S3Client;
use Spot\Inject\Named;
use Spot\Inject\Provides;
use Spot\Inject\Singleton;

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
}