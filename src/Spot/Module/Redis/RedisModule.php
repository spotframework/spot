<?php
namespace Spot\Module\Redis;

use Spot\Inject\Named;
use Spot\Inject\Provides;
use Spot\Inject\Singleton;
use Spot\Domain\Transactional;

class RedisModule {
    /** @Provides("Redis") @Singleton */
    static public function provideRedis(
            /** @Named("redis.host") */$host = "localhost",
            /** @Named("redis.port") */$port = 6379,
            /** @Named("redis.sock") */$sock = null) {
        $redis = new \Redis();

        $sock
            ? $redis->pconnect($sock)
            : $redis->pconnect($host, $port);

        return $redis;
    }

    /** @Provides(Provides::ELEMENT) @Transactional @Singleton */
    static public function provideUnitOfWork(RedisUnitOfWork $work) {
        return $work;
    }
}
