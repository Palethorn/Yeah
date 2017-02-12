<?php

if(PHP_MAJOR_VERSION == 7 && PHP_MINOR_VERSION >= 2) {
    use PHPUnit\Framework\TestCase as TestCase;
} else {
    use PHPUnit_Framework_TestCase as TestCase;
}

class DatabaseSessionHandlerTest extends TestCase {

    public function testSetWrite() {
        $session = new \Yeah\Fw\Session\DatabaseSessionHandler(array(
            'dsn' => 'mysql:host=localhost;dbname=test;charset=utf8',
            'db_user' => 'root',
            'db_password' => '',
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
            'dsn' => 'mysql:host=localhost;dbname=test;charset=utf8',
            'db_user' => 'root',
            'db_password' => '',
            'id' => 'test'
                )
        );
        $session->setSessionParam('test', 'val');
        $this->assertTrue($session->read('test'));
        $this->assertEquals('val', $session->getSessionParam('test'));
    }

    public function testRemoveWrite() {
        $session = new \Yeah\Fw\Session\DatabaseSessionHandler(array(
            'dsn' => 'mysql:host=localhost;dbname=test;charset=utf8',
            'db_user' => 'root',
            'db_password' => '',
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
            'dsn' => 'mysql:host=localhost;dbname=test;charset=utf8',
            'db_user' => 'root',
            'db_password' => '',
            'id' => 'test'
                )
        );
        $this->assertTrue($session->read('test'));
        $this->assertTrue($session->destroy(null, null));
    }

}
