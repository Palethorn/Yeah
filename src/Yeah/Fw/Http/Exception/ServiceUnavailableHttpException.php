<?php
namespace Yeah\Fw\Http\Exception;

/**
 * 
 */

class ServiceUnavailableHttpException extends HttpExceptionAbstract {
    /**
     * {@inheritdoc }
     */
    public function __construct($message = 'Service unavailable') {
        parent::__construct(503, $message);
    }
}