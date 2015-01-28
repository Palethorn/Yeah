<?php
namespace Yeah\Fw\Http\Exception;

/**
 * 
 */

class BadRequestHttpException extends HttpExceptionAbstract {
    /**
     * {@inheritdoc }
     */
    public function __construct($message = 'Bad Request') {
        parent::__construct(400, $message);
    }
}