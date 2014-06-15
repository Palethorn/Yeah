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
    public static function get($route) {

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
        $request_uri = $request->getRequestUri();
        if(($route = self::get($request_uri))) {
            $routeRequest = new $route['route_request_handler']();
            return $routeRequest->handle($route, $request);
        } else {
            $routeRequest = new RouteRequestHandler();
            return $routeRequest->handle(array(
                'controller' => $request->getParameter('controller'),
                'action' => $request->getParameter('action') ? $request->getParameter('action') : '',
                'secure' => false
                    ), $request);
        }
        return null;
    }

}
