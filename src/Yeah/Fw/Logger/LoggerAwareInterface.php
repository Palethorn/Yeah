<?php

namespace Yeah\Fw\Logger;

/**
 * Describes a logger-aware instance per PSR-3 standard recommendation
 * @link https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md
 */
interface LoggerAwareInterface
{
    /**
     * Sets a logger instance on the object
     *
     * @param LoggerInterface $logger
     * @return null
     */
    public function setLogger(LoggerInterface $logger);
}