<?php

define('DS', DIRECTORY_SEPARATOR);

require_once '..' . DS . '..' . DS . 'Yeah' . DS . 'Fw' . DS . 'Application' . DS . 'Autoloader.php';

$autoloader = new Yeah\Fw\Application\Autoloader();
$autoloader->addIncludePath('..' . DS . '..');
$autoloader->register();
$autoloader->setCache(new \Yeah\Fw\Cache\NullCache());