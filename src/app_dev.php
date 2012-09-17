<?php

use Symfony\Component\HttpFoundation\Request;

$loader = require_once __DIR__ . '/app/autoload.php';

require_once __DIR__ . '/core/AppKernel.php';

$kernel = new AppKernel('dev', true);
$kernel->loadClassCache();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);

