<?php
namespace Yeah\Fw\Routing;

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
    public function handle($options, $request) {
        $route = new Route();
        $route->setAction($options['action']);
        $route->setController($options['controller']);
        $route->setSecure($options['secure']);
        return $route;
    }

}
