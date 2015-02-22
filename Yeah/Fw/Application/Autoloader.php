<?php

namespace Yeah\Fw\Application;

/*
 * Used for for automatic loading of required files.
 * Designed for low performance hit and low resource consuption.
 * 
 * @author David Cavar
 */

class Autoloader {

    private $include_paths = array();
    private $cache = false;
    private $autoload = array();
    private $autoload_modified = false;

    /*
     * Sets the path where the autoloader should look for required files
     * 
     * @param string $inc_path Include path parameter
     * @return \Yeah\Fw\Application\Autoloader
     */

    public function addIncludePath($inc_path) {
        $this->include_paths[] = $inc_path;
        return $this;
    }

    /**
     * Getter for include path
     * 
     * @return string
     */
    public function getIncludePaths() {
        return $this->include_paths;
    }

    /**
     * Automatically loads required file based on it's namespace
     * Uses any registered directory path as library root then resolves
     * relative path based on namespace
     * 
     * @param string $class_name Class name with its appropriate namespace
     */
    function autoload($class) {
        if($this->autoloadFromCache($class)) {
            return;
        }
        $class_name = ltrim($class, '\\');
        $relative_path = '';
        $namespace = '';
        if($last_ns_pos = strrpos($class_name, '\\')) {
            $namespace = substr($class_name, 0, $last_ns_pos);
            $class_name = substr($class_name, $last_ns_pos + 1);
            $relative_path = str_replace('\\', DS, $namespace);
        }
        foreach($this->include_paths as $include_path) {
            $file_path = $include_path . DS . $relative_path . DS . str_replace('_', DS, $class_name) . '.php';
            if(file_exists($file_path)) {
                $this->autoload[$class] = $file_path;
                require $file_path;
                $this->autoload_modified = true;
                return;
            }
        }
    }

    public function autoloadFromCache($class) {
        if(isset($this->autoload[$class])) {
            require_once $this->autoload[$class];
            return true;
        }
        return false;
    }

    /**
     * Instructs autoloader to begin listening for class requirements
     * 
     * @return \Yeah\Fw\Application\Autoloader
     */
    public function register() {
        spl_autoload_register(array($this, 'autoload'));
        return $this;
    }

    public function setCache(\Yeah\Fw\Cache\CacheInterface $cache) {
        $this->cache = $cache;
        if($this->cache->has('autoload.php')) {
            $this->autoload = $this->cache->get('autoload.php');
        }
        $this->autoload_modified = false;
    }

    /**
     * 
     * Stops autoloader for further listening
     * 
     * @return \Yeah\Fw\Application\Autoloader
     */
    public function unregister() {
        spl_autoload_unregister(array($this, 'autoload'));
        return $this;
    }

    public function __destruct() {
        if($this->autoload_modified) {

            $this->cache->set('autoload.php', $this->autoload);
        }
    }

}
