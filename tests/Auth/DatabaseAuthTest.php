<?php

class DatabaseAuthTest extends TestCase {
    public function __construct($name = null, array $data = array(), $dataName = '') {
        parent::__construct($name, $data, $dataName);
        $this->session_handler = $this->getMockBuilder('\Yeah\Fw\Session\NullSessionHandler')->getMock();
        $this->database_auth = new Yeah\Fw\Auth\DatabaseAuth($this->session_handler);
    }

    public function testGetUser() {
        $this->session_handler->method('getSessionParam')->with('user')->willReturn(array('username' => 'test'));
        $this->assertEquals($this->database_auth->getUser(), array('username' => 'test'));
    }

    public function testSetUser() {
        $this->session_handler->method('getSessionParam')->with('user')->willReturn(array('username' => 'test'));
        $this->database_auth->setUser(array('username' => 'test'));
        $this->assertEquals($this->database_auth->getUser(), array('username' => 'test'));
    }

    public function testIsAuthenticated() {
        $this->session_handler->method('getSessionParam')->with('is_authenticated')->willReturn(true);
        $this->assertTrue($this->database_auth->isAuthenticated());
    }

    public function testSetAuthenticated() {
        $this->session_handler->method('getSessionParam')->with('is_authenticated')->willReturn(true);
        $this->database_auth->setAuthenticated(true);
        $this->assertTrue($this->database_auth->isAuthenticated());
    }
}
