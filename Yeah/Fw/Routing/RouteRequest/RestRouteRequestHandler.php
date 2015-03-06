<?php

namespace Yeah\Fw\Routing\RouteRequest;

class RestRouteRequestHandler implements RouteRequestHandlerInterface {

    /**
     * {@inheritdoc}
     */
    public function handle($options, \Yeah\Fw\Http\Request $request) {
        $req_method = $request->getRequestMethod();
        if(!$this->match($request->getRequestUri(), $options['pattern']) || !$this->checkMethod($options, $req_method)) {
            return false;
        }
        $route = new \Yeah\Fw\Routing\Route\Route();
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

    public function configureCache(\Yeah\Fw\Routing\Route\RouteInterface $route, $options) {
        if(isset($options['is_cacheable']) && $options['is_cacheable']) {
            $route->setIsCacheable(true);
            if(isset($options['cache_duration'])) {
                $route->setCacheDuration($options['cache_duration']);
            }
        }
    }

}
