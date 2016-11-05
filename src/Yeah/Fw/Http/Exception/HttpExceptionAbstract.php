<?php
namespace Yeah\Fw\Http\Exception;

/**
 * 
 */
abstract class HttpExceptionAbstract extends \Exception implements \Yeah\Fw\Http\Exception\HttpExceptionInterface {
    private $httpStatusCode;
    
    /**
     * Create HttpException object
     * 
     * @param int $httpStatusCode
     * @param string $message
     */
    public function __construct($httpStatusCode, $message) {
        $this->message = $message;
        $this->httpStatusCode = $httpStatusCode;
        http_response_code($httpStatusCode);
        parent::__construct($message, 0, null);
    }
    
    /**
     * {@inheritdoc }
     */
    public function getStatusCode() {
        return $this->httpStatusCode;
    }
}