<?php
namespace Yeah\Fw\Http\Exception;

/**
 * 
 */

class ForbiddenHttpException extends HttpExceptionAbstract {
    /**
     * {@inheritdoc }
     */
    public function __construct($message = 'Forbidden') {
        parent::__construct(403, $message);
    }
}