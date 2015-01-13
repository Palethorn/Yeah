<?php
namespace Yeah\Fw\Http\Exception;

/**
 * 
 */

class PreconditionFailedHttpException extends HttpExceptionAbstract {
    /**
     * {@inheritdoc }
     */
    public function __construct($message = 'Precondition failed') {
        parent::__construct(412, $message);
    }
}