<?php

namespace Yeah\Fw\Logger;

/**
 * This class provides no implementation of LoggerInterface. It should be used
 * in applications with no need for logger.
 */
class NullLogger implements \Yeah\Fw\Logger\LoggerInterface {

    public function alert($message, array $context = array()) {
        
    }

    public function critical($message, array $context = array()) {
        
    }

    public function debug($message, array $context = array()) {
        
    }

    public function emergency($message, array $context = array()) {
        
    }

    public function error($message, array $context = array()) {
        
    }

    public function info($message, array $context = array()) {
        
    }

    public function log($level, $message, array $context = array()) {
        
    }

    public function notice($message, array $context = array()) {
        
    }

    public function warning($message, array $context = array()) {
        
    }

}
