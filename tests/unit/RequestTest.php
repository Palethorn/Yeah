<?php

$_SERVER = array(
    'PHP_SELF' => '1',
    'GATEWAY_INTERFACE' => '2',
    'SERVER_ADDR' => '3',
    'SERVER_NAME' => '4',
    'SERVER_SOFTWARE' => '5',
    'SERVER_PROTOCOL' => '6',
    'REQUEST_METHOD' => '7',
    'REQUEST_TIME' => '8',
    'REQUEST_TIME_FLOAT' => '9',
    'QUERY_STRING' => '10',
    'DOCUMENT_ROOT' => '11',
    'HTTP_ACCEPT' => '12',
    'HTTP_ACCEPT_CHARSET' => '13',
    'HTTP_ACCEPT_ENCODING' => '14',
    'HTTP_ACCEPT_LANGUAGE' => '15',
    'HTTP_CONNECTION' => '16',
    'HTTP_HOST' => '17',
    'HTTP_REFERER' => '18',
    'HTTP_USER_AGENT' => '19',
    'HTTPS' => '20',
    'REMOTE_ADDR' => '21',
    'REMOTE_HOST' => '22',
    'REMOTE_PORT' => '23',
    'REMOTE_USER' => '24',
    'REDIRECT_REMOTE_USER' => '25',
    'SCRIPT_FILENAME' => '26',
    'SERVER_ADMIN' => '27',
    'SERVER_PORT' => '28',
    'SERVER_SIGNATURE' => '29',
    'PATH_TRANSLATED' => '30',
    'SCRIPT_NAME' => '31',
    'REQUEST_URI' => '32',
    'PHP_AUTH_DIGEST' => '33',
    'PHP_AUTH_USER' => '34',
    'PHP_AUTH_PW' => '35',
    'AUTH_TYPE' => '36',
    'PATH_INFO' => '37',
    'ORIG_PATH_INFO' => '38'
);

$_GET['lol'] = 10;

class RequestTest extends PHPUnit_Framework_TestCase {

    public function testGetEnvironmentParameter() {
        $keys = array(
            'PHP_SELF',
            'GATEWAY_INTERFACE',
            'SERVER_ADDR',
            'SERVER_NAME',
            'SERVER_SOFTWARE',
            'SERVER_PROTOCOL',
            'REQUEST_METHOD',
            'REQUEST_TIME',
            'REQUEST_TIME_FLOAT',
            'QUERY_STRING',
            'DOCUMENT_ROOT',
            'HTTP_ACCEPT',
            'HTTP_ACCEPT_CHARSET',
            'HTTP_ACCEPT_ENCODING',
            'HTTP_ACCEPT_LANGUAGE',
            'HTTP_CONNECTION',
            'HTTP_HOST',
            'HTTP_REFERER',
            'HTTP_USER_AGENT',
            'HTTPS',
            'REMOTE_ADDR',
            'REMOTE_HOST',
            'REMOTE_PORT',
            'REMOTE_USER',
            'REDIRECT_REMOTE_USER',
            'SCRIPT_FILENAME',
            'SERVER_ADMIN',
            'SERVER_PORT',
            'SERVER_SIGNATURE',
            'PATH_TRANSLATED',
            'SCRIPT_NAME',
            'REQUEST_URI',
            'PHP_AUTH_DIGEST',
            'PHP_AUTH_USER',
            'PHP_AUTH_PW',
            'AUTH_TYPE',
            'PATH_INFO',
            'ORIG_PATH_INFO'
        );
        $request = new \Yeah\Fw\Http\Request();
        echo PHP_EOL;
        foreach($keys as $key) {
            $val = $request->getEnvironmentParameter($key);
            $this->assertNotFalse($val);
        }
    }

    public function testGetParameter() {
        echo PHP_EOL;
        $_SERVER = array(
            'REQUEST_METHOD' => 'GET',
            'HTTP_HOST' => 'testing.yeah',
            'HTTP_USER_AGENT' => 'Test',
            'REQUEST_URI' => 'test.com/loli/20',
        );
        $request = new \Yeah\Fw\Http\Request();
        echo $request->getParameter('loli') . PHP_EOL;
        echo $request->getParameter('lol') . PHP_EOL;
        echo $request->getUrlParameter('lol') . PHP_EOL;
        echo $request->getUrlParameter('loli') . PHP_EOL;
    }

}
