<?php
namespace Yeah\Fw\Mvc;

/**
 * @property Response $response
 * @property Request $request
 * @property SessionHandler $session 
 */
class Controller {

    private $HasView = false;
    private $view = null;
    private $data = null;
    protected $session = null;
    protected $request = null;
    protected $response = null;

    public function __construct(\Yeah\Fw\Http\Request $request, \Yeah\Fw\Http\Response $response, \Yeah\Fw\Session\DatabaseSessionHandler $session, $logger) {
        $this->session = $session;
        $this->logger = $logger;
        $this->request = $request;
        $this->response = $response;
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
        $this->HasView = true;
        $this->view = new View($view);
        $flash = $this->session->getSessionParam('flash');
        if ($flash) {
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
        return $this->view;
    }

    public function getData() {
        return $this->data;
    }

    public function hasView() {
        return $this->HasView;
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
     * @return Response
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
