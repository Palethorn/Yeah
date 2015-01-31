<?php

namespace Yeah\Fw\Routing\RouteRequest;

class RestRouteRequestHandler implements RouteRequestHandlerInterface {

    /**
     * {@inheritdoc}
     */
    public function handle($options, \Yeah\Fw\Http\Request $request) {
        if(!$this->match($request->getRequestUri(), $options['pattern']) || !$this->checkMethod($options, $request->getRequestMethod())) {
            return false;
        }
        $route = new \Yeah\Fw\Routing\Route\Route();
        $route->setAction($options['restful'][$request->getRequestMethod()]['action']);
        $route->setController(new $options['controller']());
        $secure = $options['secure'] || $options['restful'][$request->getRequestMethod()]['secure'];
        $route->setSecure($secure);
        return $route;
    }

    /**
     * 
     * @param mixed $options Route options
     * @param string $method HTTP method
     * @throws \Exception
     */
    private function checkMethod($options, $method) {
        if(!isset($options['restful'][$method])) {
            return false;
        }
        return true;
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
