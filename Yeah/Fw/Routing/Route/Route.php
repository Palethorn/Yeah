<?php

namespace Yeah\Fw\Routing\Route;

class Route implements RouteInterface {

    private $is_cacheable = false;
    private $cache_duration = 1440;
    private $params = array();

    public function getAction() {
        return $this->action;
    }

    /**
     * {@inheritdoc}
     */
    public function getController() {
        return $this->controller;
    }

    /**
     * {@inheritdoc}
     */
    public function isSecure() {
        return isset($this->secure) ? $this->secure : false;
    }

    /**
     * {@inheritdoc}
     */
    public function setAction($action) {
        $this->action = $action;
    }

    /**
     * {@inheritdoc}
     */
    public function setController(\Yeah\Fw\Mvc\Controller $controller) {
        $this->controller = $controller;
    }

    /**
     * {@inheritdoc}
     */
    public function setSecure($secure) {
        $this->secure = $secure;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(\Yeah\Fw\Http\Request $request, \Yeah\Fw\Http\Response $response, \SessionHandlerInterface $session, \Yeah\Fw\Auth\AuthInterface $auth) {
        $method = $this->getAction();
        $this->getController()->setRequest($request);
        $this->getController()->setResponse($response);
        $this->getController()->setAuth($auth);
        $this->getController()->setSessionHandler($session);
        return $this->getController()->execute($method, $this->params);
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheDuration() {
        return $this->cache_duration;
    }

    /**
     * {@inheritdoc}
     */
    public function getIsCacheable() {
        return $this->is_cacheable;
    }

    /**
     * {@inheritdoc}
     */
    public function setCacheDuration($duration) {
        $this->cache_duration = $duration;
    }

    /**
     * {@inheritdoc}
     */
    public function setIsCacheable($is_cacheable) {
        $this->is_cacheable = $is_cacheable;
    }
    
    /**
     * {inheritdoc}
     */
    public function setRouteParams($params) {
        $this->params = $params;
    }

}
