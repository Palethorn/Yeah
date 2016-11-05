<?php

namespace Yeah\Fw\Cache;

class FileCache implements CacheInterface {

    private $loaded = array();
    private $default_duration = 1440;
    private $cache_dir = falsE;

    public function __construct($cache_dir, $default_duration = 1440) {
        $this->cache_dir = $cache_dir;
        $this->default_duration = $default_duration;
        if(!is_dir($cache_dir)) {
            mkdir($cache_dir, 0777, true);
        }
    }

    public function clean($duration = false) {
        if(!$duration) {
            $duration = $this->default_duration;
        }
    }

    public function get($key) {
        if(!$this->has($key)) {
            return null;
        }
        return $this->loaded[$key]['data'];
    }

    public function has($key) {
        if(isset($this->loaded[$key]) && $this->valid($this->loaded[$key])) {
            return true;
        }

        $file = $this->load($this->getFilename($key));
        if($file && $this->valid($file)) {
            $this->loaded[$key] = $file;
            return true;
        }
        $this->remove($key);
        return false;
    }

    public function persist($key, $value) {
        $this->set($key, $value, 0);
    }

    public function remove($key) {
        $filename = $this->getFilename($key);
        if(file_exists($filename)) {
            unlink($filename);
        }
        unset($this->loaded[$key]);
    }

    public function valid($file) {
        if($file['duration'] == 0) {
            return true;
        }
        if($file['ctime'] + $file['duration'] > strtotime('now')) {
            return true;
        }

        return false;
    }

    public function set($key, $value, $duration = false) {
        if(!$duration) {
            $duration = $this->default_duration;
        }
        $slot = array();
        $slot['ctime'] = strtotime('now');
        $slot['duration'] = $duration;
        $slot['data'] = $value;

        if(!$this->valid($slot)) {
            return;
        }

        $this->loaded[$key] = $slot;
        $file = new \Yeah\Fw\Filesystem\File($this->getFilename($key), 'w');
        $data = '<?php ' . PHP_EOL . PHP_EOL . 'return ' . var_export($slot, true) . ';';
        $file->write($data);
        $file->close();
    }

    public function load($filename) {
        if(file_exists($filename)) {
            $file = require_once $filename;
            return $file;
        }
        return false;
    }

    public function getFilename($key) {
        return $this->cache_dir . DIRECTORY_SEPARATOR . $key . '.cache';
    }

}
