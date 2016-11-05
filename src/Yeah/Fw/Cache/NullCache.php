<?php

namespace Yeah\Fw\Cache;

class NullCache implements \Yeah\Fw\Cache\CacheInterface {
    public function clean($duration) {
        
    }

    public function get($key) {
        return false;
    }

    public function has($key) {
        return false;
    }

    public function persist($key, $value) {
        
    }

    public function remove($key) {
        
    }

    public function set($key, $value, $duration = 0) {
        
    }

}
