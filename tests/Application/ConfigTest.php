<?php

class ConfigTest extends PHPUnit_Framework_TestCase {

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
