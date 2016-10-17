<?php

namespace Yeah\Fw\Http;

/**
 * HTTP request implementation
 *
 * @author David Cavar
 */
class Request {

    private $parameters = array();
    private $requestBody = null;
    private $envronment_parameters = array();

    /**
     * Magic method for accessing parameters by using get* pattern
     *
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public function __call($method, $args) {
        if(strpos($method, 'get') === 0) {
            $key = lcfirst(str_replace('get', '', $method));
            return $this->getEnvironmentParameter($key);
        }
        if(strpos($method, 'set') === 0) {
            $key = lcfirst(str_replace('set', '', $method));
            return $this->set($key, $args[0]);
        }
    }

    public function getEnvironmentParameter($key) {
        if(isset($this->envronment_parameters[$key])) {
            return $this->envronment_parameters[$key];
        }

        if(isset($_SERVER[$key])) {
            return $this->envronment_parameters[$key] = $_SERVER[$key];
        }
        $key1 = preg_replace_callback('/([a-z])([A-Z])/', function($matches) {
            return $matches[1] . '_' . $matches[2];
        }, $key);
        $key1 = strtoupper($key1);

        if(isset($_SERVER[$key1])) {
            return $this->envronment_parameters[$key] = $_SERVER[$key1];
        }
        return false;
    }

    /**
     * Retrieves request parameter
     *
     * @param string $key
     * @return string|boolean
     */
    public function getParameter($key) {
        if(($this->parameters[$key] = $this->getUrlParameter($key)) !== false) {
            return $this->parameters[$key];
        }
        if(($this->parameters[$key] = $this->getPostParameter($key)) !== false) {
            return $this->parameters[$key];
        }
        return null;
    }

    /**
     * Overwrites default request parameter
     *
     * @param string $key
     * @param mixed $value
     */
    public function setParameter($key, $value) {
        $this->parameters[$key] = $value;
    }

    /**
     * Retrieves all request parameters
     *
     * @return array
     */
    public function getAllParameters() {
        return array_merge($_GET, $_POST);
    }

    public function getUrlParameter($key) {
        if(isset($_GET[$key])) {
            return $_GET[$key];
        }
        $matches = array();
        if(preg_match('/(' . $key . ')\/(.+)(\/|$)/', $this->getRequestUri(), $matches)) {
            return $matches[2];
        }
        return null;
    }

    public function getPostParameter($key) {
        if(isset($_POST[$key])) {
            return $_POST[$key];
        }
        return null;
    }

    /**
     * Gets request POST content
     *
     * @return string
     */
    public function getRequestBody() {
        if($this->getContentLength() == 0) {
            return '';
        }
        if($this->requestBody) {
            return $this->requestBody;
        }
        $this->requestBody = file_get_contents('php://input');
        return $this->requestBody;
    }

    /**
     * Retrieves header value
     *
     * @param string $key
     * @return string|boolean
     */
    public function get($key) {
        return $this->getEnvironmentParameter($key);
    }

    /**
     * Sets header value
     *
     * @param set $key
     * @param mixed $value
     */
    public function set($key, $value) {
        $this->envronment_parameters[$key] = $value;
    }

    /**
     * Identifies HTTP request method (GET, POST, PUT, DELETE)
     *
     * @return string
     */
    public function getRequestMethod() {
        return $this->getEnvironmentParameter('REQUEST_METHOD');
    }

    /**
     * Return request uri without query string
     *
     * @return string
     */
    public function getRequestUri() {
        $request_uri = preg_replace("/(.*).php\/?/", '/', $this->getEnvironmentParameter('REQUEST_URI'));
        $request_uri = preg_replace('#\?' . $this->getQueryString() . '$#', '', $request_uri);
        return $request_uri;
    }

    public function getQueryString() {
        return $this->getEnvironmentParameter('QUERY_STRING');
    }

}
