<?php
namespace Yeah\Fw\Http\Exception;

/**
 * 
 */

class GoneHttpException extends HttpExceptionAbstract {
    /**
     * {@inheritdoc }
     */
    public function __construct($message = 'Gone') {
        parent::__construct(410, $message);
    }
}