<?php

class SimpleRouteTest extends PHPUnit_Framework_TestCase {

    public function __construct() {
        $this->request = new Yeah\Fw\Http\Request();
        $this->request->setRequestUri('/');
        $this->route = array(
            'pattern' => '/',
            'secure' => false,
            'method' => array(
                'GET' => (function() {
            return 'GET';
        }),
                'POST' => (function() {
            return 'POST';
        }),
                'PUT' => (function() {
            return 'PUT';
        }),
                'DELETE' => (function() {
            return 'DELETE';
        })
            )
        );
    }

    public function testGet() {
        $this->request->setRequestMethod('GET');
        $routeRequestHandler = new Yeah\Fw\Routing\RouteRequest\SimpleRouteRequestHandler();
        $route = $routeRequestHandler->handle($this->route, $this->request);
        $this->assertNotFalse($route);
        $this->assertEquals('GET', $route->execute($this->request, new \Yeah\Fw\Http\Response(), new \Yeah\Fw\Session\NullSessionHandler(), new Yeah\Fw\Auth\NullAuth()));
    }

    public function testPost() {
        $this->request->setRequestMethod('POST');
        $routeRequestHandler = new Yeah\Fw\Routing\RouteRequest\SimpleRouteRequestHandler();
        $route = $routeRequestHandler->handle($this->route, $this->request);
        $this->assertNotFalse($route);
        $this->assertEquals('POST', $route->execute($this->request, new \Yeah\Fw\Http\Response(), new \Yeah\Fw\Session\NullSessionHandler(), new Yeah\Fw\Auth\NullAuth()));
    }

    public function testPut() {
        $this->request->setRequestMethod('PUT');
        $routeRequestHandler = new Yeah\Fw\Routing\RouteRequest\SimpleRouteRequestHandler();
        $route = $routeRequestHandler->handle($this->route, $this->request);
        $this->assertNotFalse($route);
        $this->assertEquals('PUT', $route->execute($this->request, new \Yeah\Fw\Http\Response(), new \Yeah\Fw\Session\NullSessionHandler(), new Yeah\Fw\Auth\NullAuth()));
    }

    public function testDelete() {
        $this->request->setRequestMethod('DELETE');
        $routeRequestHandler = new Yeah\Fw\Routing\RouteRequest\SimpleRouteRequestHandler();
        $route = $routeRequestHandler->handle($this->route, $this->request);
        $this->assertNotFalse($route);
        $this->assertEquals('DELETE', $route->execute($this->request, new \Yeah\Fw\Http\Response(), new \Yeah\Fw\Session\NullSessionHandler(), new Yeah\Fw\Auth\NullAuth()));
    }

}
