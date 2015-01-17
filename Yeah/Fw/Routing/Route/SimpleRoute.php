<?php

namespace Yeah\Fw\Routing\Route;

class SimpleRoute extends Route {

    public function execute(\Yeah\Fw\Http\Request $request, \Yeah\Fw\Http\Response $response, \SessionHandlerInterface $session, \Yeah\Fw\Auth\AuthInterface $auth) {
        return $this->getController()->anonymous($this->getAction());
    }

}
