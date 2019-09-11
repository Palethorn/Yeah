<?php

namespace Yeah\Fw\Application;

/**
 * Used for storing dependencies in a form of factories, and services. Object starts as a storage for service factories
 * which are invoked to create actual service. Factory is invoked only when needed. Service is instanced once in
 * an application lifetime and reference is reused every other time when needed.
 *
 * @property array $factories Collection of factory methods for instantiating
 * services
 * @property services $name Collection of already instantiated services
 */
class DependencyContainer {

    private $factory_index = array(
        'type' => array(),
        'tag' => array(),
        'id' => array()
    );

    private $service_index = array(
        'type' => array(),
        'tag' => array(),
        'id' => array()
    );

    /**
     * Sets factory method for service
     *
     * @param string $key
     * @param Closure $factory
     */
    public function set($config) {
        if(isset($config['tag'])) {
            $this->registerTag($config['tag'], $config['id']);
        }

        $this->registerType($config['class'], $config['id']);
        $this->factory_index['id'][$config['id']] = $config;
    }

    /**
     * Retrieves instantiated service
     *
     * @param string $key
     * @return mixed
     */
    public function get($id) {

    }

    public function registerType($type, $id) {
        if(!isset($this->factory_index['type'][$type])) {
            $this->factory_index['type'][$type] = array();
        }

        $this->factory_index['type'][$type][] = $id;
    }

    public function registerTag($tag, $id) {
        if(!isset($this->factory_index['tag'][$tag])) {
            $this->factory_index['tag'][$tag] = array();
        }

        $this->factory_index['tag'][$tag][] = $id;
    }


}
