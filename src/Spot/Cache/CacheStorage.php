<?php
namespace Spot\Cache;

interface CacheStorage {
    function get($key);
    
    function set($key, $value);
    
    function multiGet(array $keys);
    
    function multiSet(array $keyValues);
    
    function delete($key);
    
    function multiDelete(array $keys);
    
    function contains($key);
}