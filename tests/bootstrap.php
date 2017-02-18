<?php

if(PHP_MAJOR_VERSION == 7 && PHP_MINOR_VERSION >= 2) {
    class TestCase extends PHPUnit\Framework\TestCase {}
} else {
    class TestCase extends PHPUnit_Framework_TestCase {}
}
error_reporting(E_ERROR | E_PARSE);
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

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Yeah' . DIRECTORY_SEPARATOR . 'Fw' . DIRECTORY_SEPARATOR . 'Application' . DIRECTORY_SEPARATOR . 'Autoloader.php';

$autoloader = new Yeah\Fw\Application\Autoloader();
$autoloader->addIncludePath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' .DIRECTORY_SEPARATOR . 'src');
$autoloader->register();
$autoloader->setCache(new \Yeah\Fw\Cache\NullCache());
