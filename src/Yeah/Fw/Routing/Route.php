<?php

namespace Yeah\Fw\Routing;

use Yeah\Fw\Auth\AuthInterface;
use Yeah\Fw\Http\Request;
use Yeah\Fw\Mvc\Controller;

/**
 * @property bool $is_cacheable
 * @property int $cache_duration
 * @property array $properties
 */
class Route {

    private $is_cacheable = false;
    private $cache_duration = 1440;
    private $params = array();

    /**
     * @return string
     */
    public function getAction() {
        return $this->action;
    }

    /**
     * @return string
     */
    public function getController() {
        return $this->controller;
    }

    /**
     * @return bool
     */
    public function isSecure() {
        return isset($this->secure) ? $this->secure : false;
    }

    /**
     * @param string $action
     */
    public function setAction($action) {
        $this->action = $action;
    }

    /**
     * @param Controller $controller
     */
    public function setController(string $controller) {
        $this->controller = $controller;
    }

    /**
     * @param bool $secure
     */
    public function setSecure($secure) {
        $this->secure = $secure;
    }

    /**
     * @param Request $request
     * @param SessionHandlerInterface $session
     * @param AuthInterface $auth
     * @return ResponseInterface
     */
    public function execute(Request $request, \SessionHandlerInterface $session, AuthInterface $auth) {
        $method = $this->getAction();
        $this->getController()->setRequest($request);
        $this->getController()->setAuth($auth);
        $this->getController()->setSessionHandler($session);
        return $this->getController()->execute($method, $this->params);
    }

    /**
     * @return int
     */
    public function getCacheDuration() {
        return $this->cache_duration;
    }

    /**
     * @return bool
     */
    public function getIsCacheable() {
        return $this->is_cacheable;
    }

    /**
     * @param int $duration
     */
    public function setCacheDuration(int $duration) {
        $this->cache_duration = $duration;
    }

    /**
     * @param bool $is_cacheable
     */
    public function setIsCacheable(bool $is_cacheable) {
        $this->is_cacheable = $is_cacheable;
    }
    
    /**
     * @param array $params
     */
    public function setRouteParams(array $params) {
        $this->params = $params;
    }

}
