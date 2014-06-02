<?php
namespace Yeah\Fw\Application;

/**
 * Use for storing runtime configuration
 * 
 */
class Config {
    private static $config = array();
    
    /**
     * 
     * Fetches the value under stored key
     * 
     * @param string $key Hash map key
     * @return mixed
     */
    public static function get($key) {
        if(isset(self::$config[$key])) {
            return self::$config[$key];
        }
        return false;
    }
    
    /**
     * Stores specified value under specified key
     * 
     * @param string $key Key to store value under it
     * @param type $value Value to store under certain key
     */
    public static function set($key, $value)
    {
        self::$config[$key] = $value;
    }
}
