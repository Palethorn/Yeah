<?php

namespace Yeah\Fw\Application;

class Router {

    private static $routes = array();

    public static function add($route, $params = array()) {
        if(!isset(self::$routes[$route])) {
            self::$routes[$route] = $params;
        }
    }

    public static function get($req) {
        foreach (self::$routes as $pattern => $route) {
            if(preg_match($pattern, $req)) {
                return $route;
            }
        }
        return false;
    }

    public static function remove($route) {
        unset(self::$routes[$route]);
    }

    public function handle($request) {
        $controller = $request->getParameter('controller');
        $action = $request->getParameter('action');
        $request_uri = $this->combine($controller, $action);
        if(($route = self::get($request_uri))) {
            $controller = $route['controller'];
            $request->setParameter('controller', $controller);
            if(isset($route['restful'])) {
                $this->checkMethod($route, $request->getRequestMethod());
                $action = $route['restful'][$request->getRequestMethod()];
                $request->setParameter('action', $action);
            } else {
                $request->setParameter('action', $route['action']);
            }
            return $route;
        }
        $route = array('controller' => $controller, 'action' => $action);
        return $route;
    }

    public function checkMethod($route, $method) {
        if(!isset($route['restful'][$method])) {
            throw new \Exception('No allowed methods', 405, null);
        }
    }

    public function combine($controller, $action) {
        return '/' . ($controller ? $controller : '') . ($action ? '/' . $action : '');
    }

}
