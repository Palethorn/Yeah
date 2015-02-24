<?php

class SimpleRouteTest extends PHPUnit_Framework_TestCase {

    public function __construct() {
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
        $this->request = $this->getMockBuilder('\Yeah\Fw\Http\Request')->getMock();
        $this->request->method('getRequestUri')->willReturn('/');
        
        $this->response = $this->getMockBuilder('\Yeah\Fw\Http\Response')->getMock();
        $this->session = $this->getMockBuilder('\Yeah\Fw\Session\NullSessionHandler')->getMock();
        $this->auth = $this->getMockBuilder('\Yeah\Fw\Auth\NullAuth')->getMock();
        
    }

    public function testGet() {
        $this->request->method('getRequestMethod')->willReturn('GET');
        $routeRequestHandler = new Yeah\Fw\Routing\RouteRequest\SimpleRouteRequestHandler();
        $route = $routeRequestHandler->handle($this->route, $this->request);
        $this->assertNotFalse($route);
        $this->assertEquals('GET', $route->execute($this->request, $this->response, $this->session, $this->auth));
    }

    public function testPost() {
        $this->request->method('getRequestMethod')->willReturn('POST');
        $routeRequestHandler = new Yeah\Fw\Routing\RouteRequest\SimpleRouteRequestHandler();
        $route = $routeRequestHandler->handle($this->route, $this->request);
        $this->assertNotFalse($route);
        $this->assertEquals('POST', $route->execute($this->request, $this->response, $this->session, $this->auth));
    }

    public function testPut() {
        $this->request->method('getRequestMethod')->willReturn('PUT');
        $routeRequestHandler = new Yeah\Fw\Routing\RouteRequest\SimpleRouteRequestHandler();
        $route = $routeRequestHandler->handle($this->route, $this->request);
        $this->assertNotFalse($route);
        $this->assertEquals('PUT', $route->execute($this->request, $this->response, $this->session, $this->auth));
    }

    public function testDelete() {
        $this->request->method('getRequestMethod')->willReturn('DELETE');
        $routeRequestHandler = new Yeah\Fw\Routing\RouteRequest\SimpleRouteRequestHandler();
        $route = $routeRequestHandler->handle($this->route, $this->request);
        $this->assertNotFalse($route);
        $this->assertEquals('DELETE', $route->execute($this->request, $this->response, $this->session, $this->auth));
    }

}
