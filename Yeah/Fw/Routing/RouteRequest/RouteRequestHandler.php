<?php

namespace Yeah\Fw\Routing\RouteRequest;

/**
 * Implements RouteRequestInterface handler
 */
class RouteRequestHandler implements RouteRequestHandlerInterface {

    /**
     * Returns route object from options
     * 
     * @param array $options
     * @return \Yeah\Fw\Routing\Route
     */
    public function handle($options, \Yeah\Fw\Http\Request $request) {
        if(!$this->match($request->getRequestUri(), $options['pattern'])) {
            return false;
        }
        $route = new Route();
        $route->setAction($options['action']);
        $class = '\\' . ucfirst($controller) . 'Controller';
        $controller = new $class();
        $route->setController($controller);
        $route->setSecure($options['secure']);
        return $route;
    }

    public function match($uri, $pattern) {
        $pattern = '#' . $pattern . '#';
        if(preg_match($pattern, $uri)) {
            return true;
        }
        return false;
    }

}
