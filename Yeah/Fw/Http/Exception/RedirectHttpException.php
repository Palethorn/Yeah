<?php
namespace Yeah\Fw\Http\Exception;

/**
 * 
 */

class FoundHttpException extends HttpExceptionAbstract {
    /**
     * {@inheritdoc }
     */
    public function __construct($message = 'Redirecting...') {
        parent::__construct(302, $message);
    }
}