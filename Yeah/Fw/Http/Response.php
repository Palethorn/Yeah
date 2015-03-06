<?php

namespace Yeah\Fw\Http;

/**
 * HTTP response implementation
 * 
 * @author David Cavar
 */
class Response {

    private $output = null;
    private $format = false;

    public function __construct($options = array()) {
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
        $this->header("Content-Type", $content_type);
    }

    /**
     * Writes JSON data to response
     * 
     * @param string $output
     */
    public function writeJson($output) {
        $this->header('Content-Type', 'application/json');
        return fwrite($this->output, $output);
    }

    /**
     * Writes XML data to response
     * 
     * @param string $output
     */
    public function writeXml($output) {
        $this->header('Content-Type', 'application/xml');
        return fwrite($this->output, $output);
    }

    /**
     * Writes raw content to response
     * 
     * @param mixed $output
     */
    public function writePlain($output) {
        return fwrite($this->output, $output);
    }

    /**
     * Writes content based on specified response format
     * 
     * @param string $output
     */
    public function write($output) {
        if($this->format === 'json') {
            return $this->writeJson($output);
        } else if($this->format === 'xml') {
            return $this->writeXml($output);
        }
        return $this->writePlain($output);
    }

    /**
     * Sets custom response header
     * 
     * @param string $header
     * @param string $value
     */
    public function header($header, $value) {
        header($header . ': ' . $value);
    }

    /**
     * Redirects user to another location
     * 
     * @param string $uri
     * @throws \Exception Redirect exception for terminating execution
     */
    public function redirect($uri) {
        $this->header('Location', $uri);
        throw new Exception\FoundHttpException();
    }

    /**
     * Sets user message to be displayed across redirects
     * 
     * @param string $text
     * @param string $type
     * @return \Yeah\Fw\Http\Response
     * 
     * TODO: Remove this function and its references
     */
    public function setFlash($text, $type = 'info') {
        \Yeah\Fw\Application\App::getInstance()->getSessionHandler()->setSessionParam('flash', array('text' => $text, 'type' => $type));
        return $this;
    }

    /**
     * Closes the output handler
     */
    public function __destruct() {
        fclose($this->output);
    }

}
