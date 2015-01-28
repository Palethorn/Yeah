<?php
namespace Yeah\Fw\Http\Exception;

/**
 * 
 */

class NotAcceptableHttpException extends HttpExceptionAbstract {
    /**
     * {@inheritdoc }
     */
    public function __construct($message = 'Not acceptable') {
        parent::__construct(406, $message);
    }
}