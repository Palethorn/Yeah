<?php

namespace Yeah\Fw\Application;

/**
 * Maps request URI to appropriate controller and action
 * 
 * @author David Cavar
 */
class Router {

    private static $routes = array();

    /**
     * Maps URI to controller and action
     * 
     * @param string $route URI key under which to map specified route
     * @param type $params
     */
    public static function add($route, $params = array()) {
        if(!isset(self::$routes[$route])) {
            self::$routes[$route] = $params;
        }
    }

    /**
     * Retrieves route options
     * 
     * @param string $route
     * @return mixed
     */
    public static function get($route) {
        foreach(self::$routes as $pattern => $options) {
            $pattern .= '/'; // Ending delimiter
            if(preg_match($pattern, $route)) {
                return $options;
            }
        }
        return false;
    }

    /**
     * Removes route under specified URI key
     * 
     * @param string $route URI key
     */
    public static function remove($route) {
        unset(self::$routes[$route]);
    }

    /**
     * Handles route request
     * 
     * @param \Yeah\Fw\Http\Request $request HTTP request object
     * @return mixed Route options
     */
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
                $route['action'] = $action;
            } else {
                $request->setParameter('action', $route['action']);
            }
            return $route;
        }
        $route = array('controller' => $controller, 'action' => $action);
        return $route;
    }

    /**
     * 
     * @param mixed $route Route options
     * @param string $method HTTP method
     * @throws \Exception
     */
    private function checkMethod($route, $method) {
        if(!isset($route['restful'][$method])) {
            throw new \Exception('No allowed methods', 405, null);
        }
    }

    /**
     * Combines controller and action into request URI
     * 
     * @param string $controller
     * @param string $action
     * @return string
     */
    private function combine($controller, $action) {
        return '/' . ($controller ? $controller : '') . ($action ? '/' . $action : '');
    }

}
