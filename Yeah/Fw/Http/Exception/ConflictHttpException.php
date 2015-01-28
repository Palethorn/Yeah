<?php
namespace Yeah\Fw\Http\Exception;

/**
 * 
 */

class ConflictHttpException extends HttpExceptionAbstract {
    /**
     * {@inheritdoc }
     */
    public function __construct($message = 'Conflict') {
        parent::__construct(409, $message);
    }
}