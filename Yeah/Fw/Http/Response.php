<?php
namespace Yeah\Fw\Http;

class Response {

    private $output = null;

    public function __construct($options = array()) {
        $this->output = fopen('php://output', 'w');
    }

    public function setResponseCode($code) {
        http_response_code($code);
    }

    public function setContentType($content_type) {
        $this->header("Content-Type", $content_type);
    }
    
    public function writeJson($output) {
        $this->header('Content-Type', 'application/json');
        fwrite($this->output, $output);
    }

    public function writeXml($output) {
        $this->header('Content-Type', 'application/xml');
        fwrite($this->output, $output);
    }

    public function writePlain($output) {
        fwrite($this->output, $output);
    }

    public function write($output) {
        if ($this->format === 'json') {
            $this->writeJson($output);
        } else {
            $this->writePlain($output);
        }
    }

    public function header($header, $value) {
        header($header . ': ' . $value);
    }

    public function redirect($uri) {
        $this->header('Location', $uri);
        throw new \Exception('Redirecting', 302, null);
    }

    public function setFlash($text, $type = 'info') {
        \Yeah\Fw\Application\App::getInstance()->getSessionHandler()->setSessionParam('flash', array('text' => $text, 'type' => $type));
        return $this;
    }

    public function __destruct() {
        fclose($this->output);
    }

}
