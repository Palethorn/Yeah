<?php

class FileCacheTest extends TestCase {

    public function __construct() {
        $this->file_cache = new \Yeah\Fw\Cache\FileCache('/tmp/test');
        $this->file_cache->set('test', 'test');
    }

    public function testGet() {
        $this->assertEquals($this->file_cache->get('test'), 'test');
    }

    public function testReinitializedGet() {
        $file_cache = new \Yeah\Fw\Cache\FileCache('/tmp/test');
        $this->assertEquals($file_cache->get('test'), 'test');
    }

    public function testHas() {
        $this->assertTrue($this->file_cache->has('test'));
    }

    public function testReinitializedHas() {
        $file_cache = new \Yeah\Fw\Cache\FileCache('/tmp/test');
        $this->assertTrue($file_cache->has('test'));
    }

    public function testRemove() {
        $this->file_cache->remove('test');
        $this->assertFalse($this->file_cache->has('test'));
        $this->assertNull($this->file_cache->get('test'));
        $this->file_cache->set('test', 'test');
    }
}
