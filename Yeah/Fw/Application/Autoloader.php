<?php

namespace Yeah\Fw\Application;

class Autoloader {

    private $include_path = '';

    public function setIncludePath($inc_path) {
        $this->include_path = $inc_path;
        return $this;
    }

    function autoload($class_name) {
        $class_name = ltrim($class_name, '\\');
        $file_name = '';
        $namespace = '';
        if($last_ns_pos = strrpos($class_name, '\\')) {
            $namespace = substr($class_name, 0, $last_ns_pos);
            $class_name = substr($class_name, $last_ns_pos + 1);
            $file_name = str_replace('\\', DIRECTORY_SEPARATOR, $namespace);
        }
        $file_name = $this->include_path . DIRECTORY_SEPARATOR . $file_name . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, $class_name) . '.php';
        if(file_exists($file_name)) {
            require $file_name;
        }
    }

    public function register() {
        spl_autoload_register(array($this, 'autoload'));
        return $this;
    }

    public function unregister() {
        spl_autoload_unregister(array($this, 'autoload'));
        return $this;
    }

}
