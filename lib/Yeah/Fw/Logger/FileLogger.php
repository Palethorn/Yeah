<?php
namespace Yeah\Fw\Logger;

class FileLogger {
    private $log = null;
    public function __construct($path) {
        $path = str_replace(array('/','\\'), DS, $path);
        $this->log = new \Yeah\Fw\Filesystem\File($path, 'a');
    }
    public function d($message) {
        $message = '[' . date('d-m-Y H:i:s') . '] Debug - ' . $message . PHP_EOL;
        $this->log->write($message);
    }
    
    public function e($message) {
        $message = '[' . date('d-m-Y H:i:s') . '] Error - ' . $message . PHP_EOL;
        $this->log->write($message);
    }
    
    public function i($message) {
        $message = '[' . date('d-m-Y H:i:s') . '] Info - ' . $message . PHP_EOL;
        $this->log->write($message);
    }
}
