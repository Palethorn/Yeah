<?php

namespace Yeah\Fw\Routing;

class RestRouteRequestHandler implements RouteRequestHandlerInterface {

    public function handle($options, $request) {
        $route = new Route();
        $this->checkMethod($options, $request->getRequestMethod());
        $route->setAction($options['restful'][$request->getRequestMethod()]['action']);
        $route->setController($options['controller']);
        $secure = $options['secure'] && $options['restful'][$request->getRequestMethod()]['secure'];
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
            throw new \Exception('No allowed methods', 405, null);
        }
    }

}
