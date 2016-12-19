<?php

namespace Yeah\Fw\Cache;

class RedisCache implements CacheInterface {

    private $redis;

    public function __construct($host = '127.0.0.1', $port = 6379, $prefix = 'app') {
        $this->redis = new \Redis();
        $this->redis->connect($host, $port);
        $this->redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_PHP);
        $this->redis->setOption(\Redis::OPT_PREFIX, $prefix . ':');
    }

    function has($key) {
        return $this->redis->exists($key);
    }

    function set($key, $value, $duration = 0) {
        $slot = array();
        $slot['ctime'] = time();
        $slot['duration'] = $duration;
        $slot['data'] = $value;
        $this->redis->hMSet($key, $slot);

        if($duration > 0) {
            $this->redis->expire($key, $duration);
        }
    }

    function persist($key, $value) {
        $this->redis->persist('key');
    }

    function get($key) {
        $slot = $this->redis->hGetAll($key);
        return $slot['data'];
    }

    function remove($key) {
        $this->redis->delete($key);
    }

    function clean($duration) {
        $this->redis->flushAll();
    }
}