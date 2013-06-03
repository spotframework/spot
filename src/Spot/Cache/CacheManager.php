<?php
namespace Spot\Cache;

class CacheManager implements CacheStorage {
    const SEP = '$';
    
    private $prefix,
            $storage;
    
    public function __construct($prefix, CacheStorage $storage) {
        $this->prefix = $prefix;
        $this->storage = $storage;
    }

    public function contains($key) {
        return $this->storage->contains($this->prefix.self::SEP.$key);
    }

    public function delete($key) {
        return $this->storage->delete($this->prefix.self::SEP.$key);
    }

    public function get($key) {
        return $this->storage->get($this->prefix.self::SEP.$key);
    }

    public function multiDelete(array $keys) {
        foreach($keys as $i => $key) {
            $keys[$i] = $this->prefix.self::SEP.$key;
        }
        
        return $this->storage->multiDelete($keys);
    }

    public function multiGet(array $keys) {
        foreach($keys as $i => $key) {
            $keys[$i] = $this->prefix.self::SEP.$key;
        }
        
        return $this->storage->multiGet($keys);
    }

    public function multiSet(array $keyValues) {
        $kv = array();
        foreach($keys as $key => $value) {
            $kv[$this->prefix.self::SEP.$key] = $value;
        }
        
        return $this->storage->multiSet($keyValues);
    }

    public function set($key, $value) {
        return $this->storage->set($this->prefix.self::SEP.$key, $value);
    }
}