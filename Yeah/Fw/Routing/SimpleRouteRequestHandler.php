<?php

namespace Yeah\Fw\Routing;

/**
 * Implements RouteRequestInterface handler
 */
class SimpleRouteRequestHandler implements RouteRequestHandlerInterface {

    /**
     * Returns route object from options
     * 
     * @param array $options
     * @return \Yeah\Fw\Routing\Route
     */
    public function handle($options, $request) {
        $route = new SimpleRoute();
        $route->setAction($options['method']);
        $route->setController('\Yeah\Fw\Mvc\Controller');
        $route->setSecure($options['secure']);
        return $route;
    }

}
