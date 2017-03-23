<?php

namespace Yeah\Fw\Application;

/**
 * Use for storing runtime configuration. Pretty cool class.
 *
 * Strict options:
 * - controllers_dir
 * - views_dir
 * - lib_dir
 * - base_dir
 * - web_dir
 * - cache_dir
 * - log_dir
 * - models_dir
 *
 * @author David Cavar
 */
class Config {

    private $config = array();

    /**
     * Default constructor. Imports and maps the array to appropriate properties.
     * @param mixed $array
     */
    public function __construct($array = array()) {
        $this->importArray($array);
    }

    /**
     * Magic method for getting property value, which is not declared as class property, based on array key.
     * @param string $name Represents class magic property
     */
    public function __get($name) {
        if(!isset($this->config[$name])) {
            return false;
        }
        return $this->config[$name];
    }

    /**
     * Magic method for setting property value, which is not declared as class property, based on array key.
     * @param string $name Represents class magic property
     * @param string $value This value is assigned to specified property
     */
    public function __set($name, $value) {
        if(is_array($value)) {
            $config = new Config($value);
            $this->config[$name] = $config;
            return;
        }
        $this->config[$name] = $value;
    }

    /**
     * Performs mapping of multiple properties based on array format. Invoked from constructor
     * @param mixed $array
     */
    public function importArray($array) {
        if($array == null) {
            return;
        }
        foreach($array as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Retrieves array from this level
     */
    public function toArray() {
        return $this->config;
    }

}
