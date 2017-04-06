<?php

define('__ROOT__', dirname(dirname(__FILE__)));
define('__APP_ROOT__', dirname(__FILE__));

$requires = [
  __ROOT__     . '/config/config.php',
  __ROOT__     . '/config/routes.php',
  __ROOT__     . '/config/database.php',
  __APP_ROOT__ . '/core/application.php',
  __APP_ROOT__ . '/core/exceptions.php',
  __APP_ROOT__ . '/core/request.php',
];

foreach ($requires as $file)
{
  require_once $file;
}

unset($requires, $file);

