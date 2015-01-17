<?php

namespace Yeah\Fw\Mvc;

/**
 * @property Response $response
 * @property Request $request
 * @property SessionHandler $session 
 */
class Controller {

    private $view = false;
    private $data = null;
    protected $session = null;
    protected $request = null;
    protected $response = null;
    private $options = null;

    public function __construct() {
        
    }

    /**
     * Executes controller action if implemented
     * 
     * @param string $action
     */
    public function execute($action) {
        $this->$action();
    }

    /**
     * Executes anonymous method
     * 
     * @param Closure method
     */
    public function anonymous(\Closure $method) {
        return $method();
    }

    /**
     * Sets controller view
     * 
     * @param string $view
     * @return View
     */
    public function setView($view) {
        $this->view = new View($this->options['view']);
        $flash = $this->session->getSessionParam('flash');
        if($flash) {
            $this->session->removeSessionParam('flash');
            $this->view->setMessage($flash['text'], $flash['type']);
        }
        return $this->view;
    }

    /**
     * Fetches current view
     * 
     * @return View
     */
    public function getView() {
        if(!$this->view) {
            $this->view = new \Yeah\Fw\Mvc\View($this->options['view']);
        }
        return $this->view;
    }

    /**
     * Fetches controller response data
     * 
     * @return string
     */
    public function getData() {
        return $this->data;
    }

    /**
     * Sets controller response data. Not called if the controller has a view
     * 
     * @param string $data
     */
    public function setData($data) {
        $this->data = $data;
    }

    /**
     * Sets user message to display
     * 
     * @param string $text
     * @param string $type
     * @return \Yeah\Fw\Mvc\Controller
     */
    public function setFlash($text, $type = 'info') {
        $this->session->setSessionParam('flash', array('text' => $text, 'type' => $type));
        return $this;
    }

    /**
     * Fetches HTTP response object
     * 
     * @return \Yeah\Fw\Http\Response
     */
    public function getResponse() {
        return $this->response;
    }

    /**
     * Sets HTTP response object
     * 
     * @param \Yeah\Fw\Http\Response
     */
    public function setResponse(\Yeah\Fw\Http\Response $response) {
        $this->response = $response;
    }

    /**
     * Fetches HTTP request object
     * 
     * @return Request
     */
    public function getRequest() {
        return $this->request;
    }

    /**
     * Fetches HTTP request object
     * 
     * @return Request
     */
    public function setRequest(\Yeah\Fw\Http\Request $request) {
        $this->request = $request;
    }

    /**
     * Redirects client to another URI
     * 
     * @param string $uri
     */
    public function redirect($uri) {
        $this->response->redirect($uri);
    }

}
