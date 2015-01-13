<?php
namespace Yeah\Fw\Http\Exception;

/**
 * 
 */

class NotFoundHttpException extends HttpExceptionAbstract {
    /**
     * {@inheritdoc }
     */
    public function __construct($message = 'Not found') {
        parent::__construct(404, $message);
    }
}