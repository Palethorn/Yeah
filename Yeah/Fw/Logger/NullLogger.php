<?php

namespace Yeah\Fw\Logger;

/**
 * This class provides no implementation of LoggerInterface. It should be used
 * in applications with no need for logger.
 */
class NullLogger implements \Yeah\Fw\Logger\LoggerInterface {

    /**
     * {@inheritdoc}
     */
    public function alert($message, array $context = array()) {
        
    }

    /**
     * {@inheritdoc}
     */
    public function critical($message, array $context = array()) {
        
    }

    /**
     * {@inheritdoc}
     */
    public function debug($message, array $context = array()) {
        
    }

    /**
     * {@inheritdoc}
     */
    public function emergency($message, array $context = array()) {
        
    }

    /**
     * {@inheritdoc}
     */
    public function error($message, array $context = array()) {
        
    }

    /**
     * {@inheritdoc}
     */
    public function info($message, array $context = array()) {
        
    }

    /**
     * {@inheritdoc}
     */
    public function log($level, $message, array $context = array()) {
        
    }

    /**
     * {@inheritdoc}
     */
    public function notice($message, array $context = array()) {
        
    }

    /**
     * {@inheritdoc}
     */
    public function warning($message, array $context = array()) {
        
    }

}
