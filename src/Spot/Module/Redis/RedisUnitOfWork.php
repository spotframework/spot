<?php
namespace Spot\Module\Redis;

use Spot\Domain\UnitOfWork;

class RedisUnitOfWork implements UnitOfWork {
    private $redis;

    public function __construct(\Redis $redis) {
        $this->redis = $redis;
    }

    function begin() {
        $this->redis->multi();
    }

    function commit() {
        $this->redis->exec();
    }

    function rollback() {
        $this->redis->discard();
    }
}
