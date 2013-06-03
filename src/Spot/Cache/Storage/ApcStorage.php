<?php
namespace Spot\Cache\Storage;

use Spot\Cache\CacheStorage;

class ApcStorage Implements CacheStorage {
    public function contains($key) {
        return apc_exists($key);
    }
    
    public function delete($key) {
        apc_delete($key);
    }
    
    public function multiDelete(array $keys) {
        apc_delete($keys);
    }

    public function get($key) {
        return apc_fetch($key);
    }

    public function multiGet(array $keys) {
        return apc_fetch($keys);
    }

    public function multiSet(array $keyValues) {
        apc_store($keyValues);
    }

    public function set($key, $value) {
        apc_store($key, $value);
    }
}