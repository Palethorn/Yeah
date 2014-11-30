<?php
namespace Yeah\Fw\Routing;

class Route implements RouteInterface {
    public function getAction() {
        return $this->action;
    }

    public function getController() {
        return $this->controller;
    }

    public function isSecure() {
        return isset($this->secure) ? $this->secure : false;
    }

    public function setAction($action) {
        $this->action = $action;
    }

    public function setController($controller) {
        $this->controller = $controller;
    }

    public function setSecure($secure) {
        $this->secure = $secure;
    }

}
