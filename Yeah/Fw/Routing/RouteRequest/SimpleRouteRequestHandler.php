<?php

namespace Yeah\Fw\Routing\RouteRequest;

/**
 * Implements RouteRequestInterface handler
 */
class SimpleRouteRequestHandler implements RouteRequestHandlerInterface {

    /**
     * {@inheritdoc}
     */
    public function handle($options, \Yeah\Fw\Http\Request $request) {
        $req_method = $request->getRequestMethod();
        if(!$this->match($request->getRequestUri(), $options['pattern']) || !isset($options['method'][$req_method])) {
            return false;
        }
        $route = new \Yeah\Fw\Routing\Route\SimpleRoute();
        $route->setAction($options['restful'][$req_method]['method']);
        $controller = new \Yeah\Fw\Mvc\Controller();
        $route->setController($controller);
        $route->setSecure($options['restful'][$req_method]['secure']);
        if(isset($options['restful'][$req_method]['cache'])) {
            $this->configureCache($route, $options['restful'][$req_method]['cache']);
        }
        return $route;
    }

    /**
     * {@inheritdoc}
     */
    public function match($uri, $pattern) {
        $pattern = '#^' . $pattern . '$#';
        if(preg_match($pattern, $uri)) {
            return true;
        }
        return false;
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
