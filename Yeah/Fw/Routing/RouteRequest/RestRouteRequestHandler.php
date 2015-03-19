<?php

namespace Yeah\Fw\Routing\RouteRequest;

/**
 * Implements RouteRequestInterface handler
 */
class RestRouteRequestHandler implements RouteRequestHandlerInterface {
    /**
     * {@inheritdoc}
     */
    public function handle($options, \Yeah\Fw\Http\Request $request) {
        if(($params = $this->match($options, $request)) === false) {
            return false;
        }
        $req_method = $request->getRequestMethod();
        $route = new \Yeah\Fw\Routing\Route\Route();
        $route->setRouteParams($params);
        $route->setAction($options['restful'][$req_method]['action']);
        $route->setController(new $options['controller']());
        $secure = $options['secure'] || $options['restful'][$req_method]['secure'];
        $route->setSecure($secure);
        if(isset($options['restful'][$req_method]['cache'])) {
            $this->configureCache($route, $options['restful'][$req_method]['cache']);
        }
        return $route;
    }

    /**
     * {@inheritdoc}
     */
    public function match($options, \Yeah\Fw\Http\Request $request) {
        $uri_matcher = new \Yeah\Fw\Routing\RouteMatching\UriMatcher();
        $http_method_matcher = new \Yeah\Fw\Routing\RouteMatching\HttpMethodMatcher();
        $params = $uri_matcher->match($options, $request);
        $method_match = $http_method_matcher->match($options, $request);
        if($params === false || $method_match === false) {
            return false;
        }
        return $params;
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
