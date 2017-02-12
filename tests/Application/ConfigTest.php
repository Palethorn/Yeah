<?php

if(PHP_MAJOR_VERSION == 7 && PHP_MINOR_VERSION >= 2) {
    use PHPUnit\Framework\TestCase as TestCase;
} else {
    use PHPUnit_Framework_TestCase as TestCase;
}

class ConfigTest extends TestCase {

    private $config = false;

    public function testConstruct() {
        $config = new \Yeah\Fw\Application\Config(array('key' => 'val'));
        $this->assertArrayHasKey('key', $config->toArray());
    }

    public function testImport() {
        $config = new \Yeah\Fw\Application\Config();
        $config->importArray(array('key' => 'val'));
        $this->assertArrayHasKey('key', $config->toArray());
    }

    public function testSetGet() {
        $config = new \Yeah\Fw\Application\Config();
        $config->string = 'string';
        $config->int = 3;
        $config->array = array('key' => 'val');
        $this->assertEquals($config->string, 'string');
        $this->assertEquals($config->int, 3);
        $this->assertArrayHasKey('key', $config->array->toArray());
    }
}
