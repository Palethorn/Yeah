<?php

namespace Yeah\Fw\Routing;

/**
 * Maps request URI to appropriate controller and action
 * 
 * @author David Cavar
 */
class Router {

    private static $routes = array();

    /**
     * Maps URI to route
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
    public static function match($route) {

        foreach(self::$routes as $pattern => $options) {
            $pattern = '#' . $pattern . '#';
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
     * @return RouteInterface
     */
    public function handle($request) {
        foreach(self::$routes as $pattern => $options) {
            $routeRequest = new $options['route_request_handler']();
            $options['pattern'] = $pattern;
            $route = $routeRequest->handle($options, $request);
            if($route) {
                return $route;
            }
        }
        $class = '\\' . ucfirst($request->getParameter('controller')) . 'Controller';
        if(!class_exists($class)) {
            throw new \Yeah\Fw\Http\Exception\NotFoundHttpException();
        }
        $route = new \Yeah\Fw\Routing\Route\Route();
        $route->setAction($request->getParameter('action') . '_action');
        $controller = new $class();
        $route->setController($controller);
        $route->setSecure(false);
        return $route;
    }

}
