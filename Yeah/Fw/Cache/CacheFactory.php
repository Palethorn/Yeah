<?php
namespace Yeah\Fw\Cache;

class CacheFactory {
    const FILE = 'FileCache';
    public static function create($type = null, $options = array()) {
        if($type === CacheFactory::FILE) {
            return new \Yeah\Fw\Cache\FileCache($options['cache_dir'], $options['default_duration']);
        }
        return new NullCache();
    }
}

