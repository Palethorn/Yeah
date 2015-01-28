<?php
namespace Yeah\Fw\Http\Exception;

/**
 * 
 */

class MethodNotAllowedHttpException extends HttpExceptionAbstract {
    /**
     * {@inheritdoc }
     */
    public function __construct($message = 'Method not allowed') {
        parent::__construct(405, $message);
    }
}