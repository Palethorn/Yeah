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
        if(!$this->match($request->getRequestUri(), $options['pattern'])) {
            return false;
        }
        $route = new \Yeah\Fw\Routing\Route\Route();
        $route->setAction($options['action']);
        $class = '\\' . ucfirst($options['controller']) . 'Controller';
        $controller = new $class();
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
