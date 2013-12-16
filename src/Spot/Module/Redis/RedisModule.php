<?php
namespace Spot\Module\Redis;

use Spot\Inject\Named;
use Spot\Inject\Provides;
use Spot\Inject\Singleton;

class RedisModule {
    /** @Provides("Redis") @Singleton */
    static public function provideRedis(
            /** @Named("redis.host") */$host = "localhost",
            /** @Named("redis.port") */$port = 6379,
            /** @Named("redis.sock") */$sock = null) {
        return $sock
            ? new \Redis($sock)
            : new \Redis($host, $port);
    }
}
