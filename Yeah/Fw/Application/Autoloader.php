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
    function autoload($class_name) {
        $class_name = ltrim($class_name, '\\');
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
                require $file_path;
                return;
            }
        }
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

}
