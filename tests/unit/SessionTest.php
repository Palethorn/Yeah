<?php

class DatabaseSessionHandlerTest extends PHPUnit_Framework_TestCase {

    public function testSetWrite() {
        $session = new \Yeah\Fw\Session\DatabaseSessionHandler(array(
            'dsn' => 'mysql:host=localhost;dbname=yeahblog;charset=utf8',
            'db_user' => 'root',
            'db_password' => 'test',
            'id' => 'test'
                )
        );
        $this->assertTrue($session->read('test'));
        $session->setSessionParam('test', 'val');
        $this->assertEquals('val', $session->getSessionParam('test'));
        $this->assertTrue($session->write(null, null));
    }

    public function testGetExisting() {
        $session = new \Yeah\Fw\Session\DatabaseSessionHandler(array(
            'dsn' => 'mysql:host=localhost;dbname=yeahblog;charset=utf8',
            'db_user' => 'root',
            'db_password' => 'test',
            'id' => 'test'
                )
        );
        $this->assertTrue($session->read('test'));
        $this->assertEquals('val', $session->getSessionParam('test'));
    }

    public function testRemoveWrite() {
        $session = new \Yeah\Fw\Session\DatabaseSessionHandler(array(
            'dsn' => 'mysql:host=localhost;dbname=yeahblog;charset=utf8',
            'db_user' => 'root',
            'db_password' => 'test',
            'id' => 'test'
                )
        );
        $this->assertTrue($session->read('test'));
        $session->removeSessionParam('test');
        $this->assertFalse($session->getSessionParam('test'));
        $this->assertTrue($session->write(null, null));
    }

    public function testDestroy() {
        $session = new \Yeah\Fw\Session\DatabaseSessionHandler(array(
            'dsn' => 'mysql:host=localhost;dbname=yeahblog;charset=utf8',
            'db_user' => 'root',
            'db_password' => 'test',
            'id' => 'test'
                )
        );
        $this->assertTrue($session->read('test'));
        $this->assertTrue($session->destroy(null, null));
    }

}
