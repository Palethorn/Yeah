<?php

class RedisCacheTest extends TestCase {

    public function __construct() {
        $this->cache = new \Yeah\Fw\Cache\RedisCache();
        $this->cache->set('test', 'test');
    }

    public function testGet() {
        $this->assertEquals($this->cache->get('test'), 'test');
    }

    public function testReinitializedGet() {
        $cache = new \Yeah\Fw\Cache\RedisCache();
        $this->assertEquals($cache->get('test'), 'test');
    }

    public function testHas() {
        $this->assertTrue($this->cache->has('test'));
    }

    public function testReinitializedHas() {
        $cache = new \Yeah\Fw\Cache\RedisCache();
        $this->assertTrue($cache->has('test'));
    }

    public function testRemove() {
        $this->cache->remove('test');
        $this->assertFalse($this->cache->has('test'));
        $this->assertNull($this->cache->get('test'));
        $this->cache->set('test', 'test');
    }

    public function testArray() {
        $this->cache->set('arr1', array('key' => 'value'));
        $data = $this->cache->get('arr1');
        $this->assertArrayHasKey('key', $data);
        $this->assertContains('value', $data);
    }

    public function testLeveledArray() {
        $this->cache->set('arr2', array('key' => array(
            array('key1' => 'value'),
            array('value1')
        )));

        $data = $this->cache->get('arr2');
        $this->assertTrue(isset($data['key']));
        $this->assertTrue(isset($data['key'][0]));
        $this->assertTrue(isset($data['key'][0]['key1']));
        $this->assertTrue(isset($data['key'][1][0]));

        $this->assertEquals($data['key'][0]['key1'], 'value');
        $this->assertEquals($data['key'][1][0], 'value1');
    }
}
