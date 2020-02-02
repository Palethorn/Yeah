<?php

namespace Yeah\Fw\Http;

/**
 * HTTP response implementation
 * 
 * @author David Cavar
 */
class Response implements ResponseInterface {

    private $output = null;
    private $headers = array();
    private $data = null;

    public function __construct($data, $status = 200, $headers = array()) {
        $this->headers = array();
        $this->data = $data;
        $this->setResponseCode($status);
        $this->setHeaders($headers);
        $this->output = fopen('php://output', 'w');
    }

    /**
     * Sets response status code
     * 
     * @param int $code
     */
    public function setResponseCode($code) {
        http_response_code($code);
    }

    /**
     * Sets response content type header
     * 
     * @param string $content_type
     */
    public function setContentType($content_type) {
        $this->setHeader("Content-Type", $content_type);
    }

    /**
     * Writes content based on specified response format
     * 
     * @param string $output
     */
    public function write() {
        $this->writeHeaders();        
        return fwrite($this->output, $this->data);
    }

    /**
     * Sets custom response header
     * 
     * @param string $header
     * @param string $value
     * @param bool $overwrite Indicates if the header should be overwritten if
     * its already set. Default true
     */
    public function setHeader($header, $value, $overwrite = true) {
        if($this->hasHeader($header) && !$overwrite) {
            return;
        }
        
        $this->headers[$header] = $value;
    }
    
    /**
     * Sets collection of headers
     * 
     * @param string $header
     * @param string $value
     */
    public function setHeaders($collection) {
        $this->headers = array_merge($this->headers, $collection);
    }
    
    /**
     * Gets custom response header
     * 
     * @param string $header
     * @return string
     */
    public function getHeader($header) {
        if(!$this->hasHeader($header)) {
            return null;
        }
        
        return $this->headers[$header];
    }

    /**
     * Checks if header is set
     * 
     * @param string $header
     * @return bool
     */
    public function hasHeader($header) {
        return isset($this->headers[$header]);
    }
    
    /**
     * Writes headers to output
     */
    public function writeHeaders() {
        foreach($this->headers as $header => $value) {
            header($header . ': ' .  $value);
        }
    }
    
    /**
     * Redirects user to another location
     * 
     * @param string $uri
     * @throws \Exception Redirect exception for terminating execution
     */
    public function redirect($uri) {
        header('Location: ' . $uri);
        throw new Exception\FoundHttpException();
    }

    /**
     * Closes the output handler
     */
    public function __destruct() {
        fclose($this->output);
    }

}
