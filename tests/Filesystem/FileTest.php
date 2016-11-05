<?php

class FileTest extends PHPUnit_Framework_TestCase
{

    public function __construct()
    {
        $this->filename = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tempfile';
        $this->file = new \Yeah\Fw\Filesystem\File($this->filename, 'w+');
    }

    public function testOpened()
    {
        $this->assertTrue($this->file->opened());
    }

    public function testWrite()
    {
        $nob = $this->file->write("test");
        $this->assertEquals(strlen('test'), $nob);
    }

    public function testRead()
    {
        $data = $this->file->read(strlen('test'));
        $this->assertEquals('test', $data);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionCode 500
     * @expectedExceptionMessage Invalid operation.
     */
    public function testWrongMode() {
        $file = new \Yeah\Fw\Filesystem\File(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tempfile2', 'w');
        $data = $file->read(strlen('test'));
        $this->assertEquals('test', $data);
        $file->close();
    }

    public function testReinitializedRead()
    {
        $file = new \Yeah\Fw\Filesystem\File($this->filename, 'r');
        $data = $file->read(strlen('test'));
        $this->assertEquals('test', $data);
        $file->close();
    }

    public function __destruct()
    {
        $this->file->close();
        unlink($this->filename);
    }
}
