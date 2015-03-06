<?php

namespace Yeah\Fw\Application;

/**
 * Use for storing runtime configuration
 * 
 * @author David Cavar
 */
class Config {

    private $config = array();

    public function __construct($array = array()) {
        $this->importArray($array);
    }

    public function __get($name) {
        if(!isset($this->config[$name])) {
            return false;
        }
        return $this->config[$name];
    }

    public function __set($name, $value) {
        if(is_array($value)) {
            $config = new Config($value);
            $this->config[$name] = $config;
            return;
        }
        $this->config[$name] = $value;
    }

    public function importArray($array) {
        if($array == null) {
            return;
        }
        foreach($array as $key => $value) {
            $this->$key = $value;
        }
    }

    public function toArray() {
        return $this->config;
    }
    
}
