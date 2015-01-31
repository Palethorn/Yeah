<?php

namespace Yeah\Fw\Application;

/**
 * @property array $factories Collection of factory methods for instantiating
 * services
 * @property services $name Collection of already instantiated services
 */
class DependencyContainer {

    private $factories = array();
    private $services = array();
    
    /**
     * Sets factory method for service
     * 
     * @param string $key
     * @param Closure $factory
     */
    public function set($key, $factory) {
        $this->factories[$key] = $factory;
    }
    
    /**
     * Retrieves instantiated service
     * 
     * @param string $key
     * @return mixed
     */
    public function get($key) {
        if(!isset($this->services[$key])) {
            $this->services[$key] = $this->factories[$key]();
        }
        return $this->services[$key];
    }
}
