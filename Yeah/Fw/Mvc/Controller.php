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

    public function __construct($options) {
        $this->options = $options;
        $this->session = $options['session'];
        $this->logger = $options['logger'];
        $this->request = $options['request'];
        $this->response = $options['response'];
    }

    public function execute($action) {
        $this->$action($this->request);
    }

    /**
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
     * 
     * @return View
     */
    public function getView() {
        if(!$this->view) {
            $this->view = new \Yeah\Fw\Mvc\View($this->options['view']);
        }
        return $this->view;
    }

    public function getData() {
        return $this->data;
    }

    public function setData($data) {
        $this->data = $data;
    }

    public function setFlash($text, $type = 'info') {
        $this->session->setSessionParam('flash', array('text' => $text, 'type' => $type));
        return $this;
    }

    /**
     * 
     * @return \Yeah\Fw\Http\Response
     */
    public function getResponse() {
        return $this->response;
    }

    /**
     * 
     * @return Request
     */
    public function getRequest() {
        return $this->request;
    }

    public function redirect($uri) {
        $this->response->redirect($uri);
    }

}
