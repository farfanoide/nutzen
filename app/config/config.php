<?php

$aliases = [
  // Core
  'Application' => 'App\Core\Application',
  'Controller'  => 'App\Core\Controller',
  'Engine'      => 'App\Core\Engine',
  'Request'     => 'App\Core\Request',
  'Response'    => 'App\Core\Response',
  'Route'       => 'App\Core\Route',
  'Router'      => 'App\Core\Router',
  'Session'     => 'App\Core\Session',
  'View'        => 'App\Core\View',

  // Controllers
  'ApplicationController' => 'App\Controllers\ApplicationController',

  // Exceptions
  'NotFoundException'      => 'App\Core\NotFoundException',
  'InternalErrorException' => 'App\Core\InternalErrorException',
  'UnauthorizedException'  => 'App\Core\UnauthorizedException',
];

foreach ($aliases as $alias => $namespace) {
  class_alias($namespace, $alias);
}

unset($alias, $namespace);
