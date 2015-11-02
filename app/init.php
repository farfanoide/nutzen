<?php

define('__ROOT__', dirname(__FILE__));

$requires = [
  '/../vendor/autoload.php',
  '/config/config.php',
  '/config/routes.php',
  '/config/database.php',
];

foreach ($requires as $file)
{
  require_once __ROOT__ . $file;
}

unset($includes, $file);

