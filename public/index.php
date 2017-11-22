<?php

use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Dotenv\Dotenv;

$loader = require __DIR__.'/../vendor/autoload.php';

if (isset($_ENV['APP_ENV'])) {
    $_SERVER['APP_ENV'] = $_ENV['APP_ENV'];
}

if (isset($_ENV['APP_DEBUG'])) {
    $_SERVER['APP_DEBUG'] = $_ENV['APP_DEBUG'];
}

if ($_SERVER['APP_DEBUG'] ?? ('prod' !== ($_SERVER['APP_ENV'] ?? 'dev'))) {
    umask(0000);
    Debug::enable();
}

$kernel = new Kernel($_SERVER['APP_ENV'] ?? 'dev', $_SERVER['APP_DEBUG'] ?? ('prod' !== ($_SERVER['APP_ENV'] ?? 'dev')));
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
