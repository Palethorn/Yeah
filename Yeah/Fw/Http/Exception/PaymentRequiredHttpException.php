<?php
namespace Yeah\Fw\Http\Exception;

/**
 * 
 */

class PaymentRequiredHttpException extends HttpExceptionAbstract {
    /**
     * {@inheritdoc }
     */
    public function __construct($message = 'Payment Required') {
        parent::__construct(402, $message);
    }
}