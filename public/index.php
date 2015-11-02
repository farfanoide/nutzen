<?php

// -----------------------------------------------------------
// requerimos todas nuestras dependencias
// -----------------------------------------------------------

require_once dirname(__DIR__) . '/app/init.php';

// -----------------------------------------------------------
// Instanciamos nuestra aplicación
// -----------------------------------------------------------

$app = new Application();

// -----------------------------------------------------------
// La ejecutamos con el request actual y recibimos un response
// -----------------------------------------------------------

$response = $app->run(Request::current());

// -----------------------------------------------------------
// Finalmente retornamos la respuesta al servidor web
// -----------------------------------------------------------

$response->send();
