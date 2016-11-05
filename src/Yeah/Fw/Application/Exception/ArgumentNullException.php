<?php
namespace Yeah\Fw\Application\Exception;

class ArgumentNullException extends \Exception {
    public function __construct($message = 'Tried to access null value', $code = 0, $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
