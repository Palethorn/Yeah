<?php

namespace Yeah\Fw\Routing\Route;

class Route implements RouteInterface {

    public function getAction() {
        return $this->action;
    }

    /**
     * 
     * @return \Yeah\Fw\Mvc\Controller
     */
    public function getController() {
        return $this->controller;
    }

    public function isSecure() {
        return isset($this->secure) ? $this->secure : false;
    }

    public function setAction($action) {
        $this->action = $action;
    }

    public function setController(\Yeah\Fw\Mvc\Controller $controller) {
        $this->controller = $controller;
    }

    public function setSecure($secure) {
        $this->secure = $secure;
    }

    public function execute(\Yeah\Fw\Http\Request $request, \Yeah\Fw\Http\Response $response, \SessionHandlerInterface $session, \Yeah\Fw\Auth\AuthInterface $auth) {
        $method = $this->getAction();
        $this->getController()->setRequest($request);
        $this->getController()->setResponse($response);
        $this->getController()->execute($method);
    }

}
