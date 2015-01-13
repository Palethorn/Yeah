<?php
namespace Yeah\Fw\Http\Exception;

/**
 * 
 */

class UnauthorizedHttpException extends HttpExceptionAbstract {
    /**
     * {@inheritdoc }
     */
    public function __construct($message = 'Unauthorized') {
        parent::__construct(401, $message);
    }
}