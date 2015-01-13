<?php
namespace Yeah\Fw\Http\Exception;

/**
 * 
 */

class LengthRequiredHttpException extends HttpExceptionAbstract {
    /**
     * {@inheritdoc }
     */
    public function __construct($message = 'Length Required') {
        parent::__construct(411, $message);
    }
}