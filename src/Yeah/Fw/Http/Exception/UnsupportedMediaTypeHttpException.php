<?php
namespace Yeah\Fw\Http\Exception;

/**
 * 
 */

class UnsupportedMediaTypeHttpException extends HttpExceptionAbstract {
    /**
     * {@inheritdoc }
     */
    public function __construct($message = 'Unsupported media type') {
        parent::__construct(415, $message);
    }
}