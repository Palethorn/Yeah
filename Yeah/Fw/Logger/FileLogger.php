<?php

namespace Yeah\Fw\Logger;

use Yeah\Fw\Logger\LogLevel;

/**
 * Implements LoggerInterface as per PSR-3 standard recommendation
 * @link https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md
 */
class FileLogger implements \Yeah\Fw\Logger\LoggerInterface {

    private $log = null;
    private $level = null;

    /**
     * {@inheritdoc}
     */
    public function __construct($path, $log_level) {
        $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR,$path);
        $this->log = new \Yeah\Fw\Filesystem\File($path, 'a');
        $this->level = $log_level;
    }

    /**
     * {@inheritdoc}
     */
    public function emergency($message, array $context = array()) {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function alert($message, array $context = array()) {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function critical($message, array $context = array()) {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function error($message, array $context = array()) {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function warning($message, array $context = array()) {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function notice($message, array $context = array()) {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function info($message, array $context = array()) {
        $this->log(LogLevel::INFO, $message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function debug($message, array $context = array()) {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function log($level, $message, array $context = array()) {
        if($level <= $this->level) {
            $this->log->write($message . PHP_EOL);
        }
    }

    /**
     * {@inheritdoc}
     */
    function interpolate($message, array $context = array()) {
        // build a replacement array with braces around the context keys
        $replace = array();
        foreach ($context as $key => $val) {
            $replace['{' . $key . '}'] = $val;
        }
        // interpolate replacement values into the message and return
        return strtr($message, $replace);
    }

}
