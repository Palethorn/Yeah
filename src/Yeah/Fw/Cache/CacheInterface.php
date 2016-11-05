<?php
namespace Yeah\Fw\Cache;

interface CacheInterface {
    function has($key);
    function set($key, $value, $duration);
    function persist($key, $value);
    function get($key);
    function remove($key);
    function clean($duration);
}
