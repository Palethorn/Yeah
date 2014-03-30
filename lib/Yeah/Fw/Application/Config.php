<?php
namespace Yeah\Fw\Application;

class Config {
    private static $config = array();
    public static function get($key) {
        if(isset(self::$config[$key])) {
            return self::$config[$key];
        }
        return false;
    }
    
    public static function set($key, $value)
    {
        self::$config[$key] = $value;
    }
}
