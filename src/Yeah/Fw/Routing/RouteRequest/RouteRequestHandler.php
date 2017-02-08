<?php

namespace Yeah\Fw\Routing\RouteRequest;

/**
 * Implements RouteRequestInterface handler
 */
class RouteRequestHandler implements RouteRequestHandlerInterface {
    /**
     * {@inheritdoc}
     */
    public function handle($options, \Yeah\Fw\Http\Request $request) {

        if(($params = $this->match($options, $request)) === false) {
            return false;
        }
        $route = new \Yeah\Fw\Routing\Route\Route();
        $route->setRouteParams($params);
        $route->setAction($options['action']);
        $class = $options['controller'];
        $controller = new $class();
        $route->setController($controller);
        $route->setSecure($options['secure']);
        if(isset($options['cache'])) {
            $this->configureCache($route, $options['cache']);
        }
        return $route;
    }

    /**
     * {@inheritdoc}
     */
    public function match($options, \Yeah\Fw\Http\Request $request) {
        $matcher = new \Yeah\Fw\Routing\RouteMatching\UriMatcher();
        return $matcher->match($options, $request);
    }

    public function configureCache(\Yeah\Fw\Routing\Route\RouteInterface $route, $options) {
        if(isset($options['is_cacheable']) && $options['is_cacheable']) {
            $route->setIsCacheable(true);
            if(isset($options['cache_duration'])) {
                $route->setCacheDuration($options['cache_duration']);
            }
        }
    }

}
