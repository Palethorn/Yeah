<?php
namespace Yeah\Fw\Application\Exception;

/**
 * Implementation of Exception for a more specific handling
 */
class ArgumentNullException extends \Exception {

    /**
     * Default constructor
     * @param string $message Short description of what happened
     * @param integer $code Identifier matching a more descriptive message
     * @param $previous If the exception is nested this represents previous exception
     */
    public function __construct($message = 'Tried to access null value', $code = 0, $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
