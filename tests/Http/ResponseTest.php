<?php

class ResponseTest extends TestCase {
    public function testResponseHeadering() {
        $response = new \Yeah\Fw\Http\Response();
        $response->setHeader('Test-Header', 'test');
        $response->setHeaders(array(
            'H1' => '1',
            'H2' => '2'
        ));

        $this->assertEquals($response->getHeader('Test-Header'), 'test');
        $this->assertEquals($response->getHeader('H1'), '1');
        $this->assertEquals($response->getHeader('H2'), '2');
        $this->assertTrue($response->hasHeader('Test-Header'));
        $this->assertTrue($response->hasHeader('H1'));
        $this->assertTrue($response->hasHeader('H2'));
        $this->assertFalse($response->hasHeader('No-Header'));
    }
}
