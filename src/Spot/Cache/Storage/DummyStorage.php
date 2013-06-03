<?php
namespace Spot\Cache\Storage;

use Spot\Cache\CacheStorage;

class DummyStorage Implements CacheStorage {
    private $vars = array();
    
    public function contains($key) {
        return isset($this->vars[$key]);
    }

    public function delete($key) {
        unset($this->vars[$key]);
    }

    public function get($key) {
        if(isset($this->vars[$key])) {
            return $this->vars[$key];
        }
    }

    public function multiDelete(array $keys) {
        foreach($keys as $key) {
            $this->delete($key);
        }
    }

    public function multiGet(array $keys) {
        $values = array();
        foreach($keys as $key) {
            $values[$key] = $this->get($key);
        }
        
        return $values;
    }

    public function multiSet(array $keyValues) {
        foreach($keyValues as $key => $value) {
            $this->set($key, $value);
        }
    }

    public function set($key, $value) {
        $this->vars[$key] = $value;
    }
}