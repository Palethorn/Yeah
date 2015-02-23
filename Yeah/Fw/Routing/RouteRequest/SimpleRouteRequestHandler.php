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
        $route->setAction($options['method'][$req_method]);
        $controller = new \Yeah\Fw\Mvc\Controller();
        $route->setController($controller);
        $route->setSecure($options['secure']);
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

}
