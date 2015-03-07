<?php

require_once '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Yeah' . DIRECTORY_SEPARATOR . 'Fw' . DIRECTORY_SEPARATOR . 'Application' . DIRECTORY_SEPARATOR . 'Autoloader.php';

$autoloader = new Yeah\Fw\Application\Autoloader();
$autoloader->addIncludePath('..' . DIRECTORY_SEPARATOR . '..');
$autoloader->register();
$autoloader->setCache(new \Yeah\Fw\Cache\NullCache());