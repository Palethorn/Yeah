<?php
namespace Yeah\Fw\Http;


class Request {

    private $headers = array();
    private $parameters = array();
    private $method = '';

    public function __construct() {
        $this->retrieveRequestHeaders();
        $this->parseParameters();
    }

    public function __call($method, $args) {
        if(strpos($method, 'get') == 0) {
            $key = strtolower(str_replace('get', '', $method));
            return $this->get($key);
        }
    }

    public function retrieveRequestHeaders() {
        foreach($_SERVER as $key => $value) {
            $key = strtolower(str_replace(array('-', '_', 'HTTP'), '', $key));
            $this->headers[$key] = $value;
        }
    }

    public function getParameter($key) {
        if(isset($this->parameters[$key])) {
            return $this->parameters[$key];
        } else {
            return false;
        }
    }

    public function setParameter($key, $value) {
        $this->parameters[$key] = $value;
    }

    public function getAllParameters() {
        return $this->parameters;
    }

    public function parseParameters() {
        $this->parseGetParameters();
        $this->parseRequestBody();
    }

    public function parseGetParameters() {
        $params = str_replace('?', '/', $this->getRequestUri());
        $params = str_replace('=', '/', $params);
        $params = str_replace('&', '/', $params);
        $params = explode('/', $params);
        array_shift($params);
        if(strpos($params[0], '.php') != FALSE) {
            array_shift($params);
        }
        $this->parameters['controller'] = isset($params[0]) ? $params[0] : '';
        $this->parameters['action'] = isset($params[1]) ? $params[1] : NULL;
        for($i = 0; $i < (count($params)); $i++) {
            $next = $i + 1;
            $this->parameters[$params[$i]] = isset($params[$next]) ? $params[$next] : NULL;
        }
    }

    public function parseRequestBody() {
        if($this->getContentLength() == 0)
            return;
        $fp = fopen('php://input', 'r');
        $this->requestBody = fread($fp, $this->getContentLength());
        fclose($fp);
        $params = explode('&', $this->requestBody);
        foreach($params as $param) {
            $pair = explode('=', $param);
            if(count($pair) == 0) {
                continue;
            }
            if(count($pair) == 1) {
                $this->parameters[$pair[0]] = null;
                continue;
            }
            $this->parameters[$pair[0]] = urldecode($pair[1]);
        }
    }

    public function getRequestBody() {
        return $this->requestBody;
    }

    public function get($key) {
        if(isset($this->headers[$key])) {
            return $this->headers[$key];
        } else {
            return false;
        }
    }

    public function set($key, $value) {
        $this->headers[$key] = $value;
    }

    public function getRequestMethod() {
        return $_SERVER['REQUEST_METHOD'];
    }

}
