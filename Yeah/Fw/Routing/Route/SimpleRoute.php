<?php

namespace Yeah\Fw\Routing\Route;

class SimpleRoute extends Route {
    
    /**
     * Executes anonymous lambda function related to requested route
     * SimpleRoute is used for mapping URIs to closures
     * 
     * @param \Yeah\Fw\Http\Request $request
     * @param \Yeah\Fw\Http\Response $response
     * @param \SessionHandlerInterface $session
     * @param \Yeah\Fw\Auth\AuthInterface $auth
     * @return mixed
     */
    public function execute(\Yeah\Fw\Http\Request $request, \Yeah\Fw\Http\Response $response, \SessionHandlerInterface $session, \Yeah\Fw\Auth\AuthInterface $auth) {
        return $this->getController()->anonymous($this->getAction());
    }

}
