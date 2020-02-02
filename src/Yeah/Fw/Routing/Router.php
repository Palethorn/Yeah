<?php

namespace Yeah\Fw\Routing;

use Yeah\Fw\Http\Request;
use Yeah\Fw\Routing\RouteRequestHandler;

/**
 * Maps request URI to appropriate controller and action
 * 
 * @author David Cavar
 */
class Router {

    private $routes = array();

    public function __construct() {
        $this->routeRequestHandler = new RouteRequestHandler();
    }

    /**
     * Maps URI to route
     * 
     * @param string $route URI key under which to map specified route
     * @param type $params
     */
    public function add($route, $params = array()) {
        $this->routes[$route] = $params;
    }

    /**
     * Retrieves route config
     * 
     * @param string $route URI key under which to map specified route
     */
    public function get($route) {
        if(!isset($this->routes[$route])) {
            return false;
        }
        
        return $this->routes[$route];
    }

    /**
     * Removes route under specified URI key
     * 
     * @param string $route URI key
     */
    public function remove($route) {
        unset($this->routes[$route]);
    }

    /**
     * Handles route request
     * 
     * @param Request $request
     * @return RouteInterface
     */
    public function handle(\Yeah\Fw\Http\Request $request) {
        foreach($this->routes as $pattern => $options) {
            $options['pattern'] = $pattern;
            $route = $this->routeRequestHandler->handle($options, $request);

            if($route) {
                return $route;
            }
        }

        throw new \Yeah\Fw\Http\Exception\NotFoundHttpException();
    }

    function matchDynamic(Request $request) {
        $matches = array();

        if(preg_match('/\/(.*)\/(.*)/', $request->getRequestUri(), $matches)) {
            $request->set('controller', $matches[1]);
            $request->set('action', $matches[2]);
            return true;
        }

        return false;
    }
}
