<?php

require_once __APP_ROOT__ . '/core/act_as_singleton.php';
require_once __APP_ROOT__ . '/core/route.php';

foreach (glob(__APP_ROOT__ . '/controllers/*.php') as $file)
{
  require_once $file;
}

class Router
{

  use ActAsSingleton;

  public $separator = '#';

  // Valiendonos de estos verbos, podemos entonces optimizar un poco la
  // busqueda de un match entre las rutas reduciendo la comparacion unicamente
  // a aquellas determinadas especificamente para un verbo en particular, lo
  // cual nos brinda ademas una pequenia capa extra de seguridad extra ya que
  // no permitiriamos jamaz que se ejecute codigo "destructivo" preparado para
  // un request por `POST` cuando el request atendido haya sido recibido, por
  // ejemplo, por `GET`.

  public $routes = [
    'GET'    => [],
    'POST'   => [],
    'PUT'    => [],
    'DELETE' => [],
  ];

  public function dispatch($request)
  {
    list($controller, $action) = $this->findHandlerFor($request);

    return (new $controller())->execute($action, $request);
  }

  protected function findHandlerFor($request)
  {

    foreach ($this->routes[$request->method] as $pattern => $handler)
    {
      $route = new Route($pattern);

      if ($route->matches($request->uri))
      {
        $request->addParams($route->paramsFrom($request->uri));

        return $this->splitHandler($handler);
      }
    }

    throw new NotFoundException();
  }

  protected function splitHandler($handler)
  {
    return explode($this->separator, $handler);
  }


  public function addRoute($method, $pattern, $handler)
  {
    $this->routes[$method][$pattern] = $handler;
  }


  // -----------------------------------------------------------
  // == Helpers
  // -----------------------------------------------------------

  public static function get($pattern, $handler)
  {
    self::getInstance()->addRoute('GET', $pattern, $handler);
  }

  public static function post($pattern, $handler)
  {
    self::getInstance()->addRoute('POST', $pattern, $handler);
  }

  public static function put($pattern, $handler)
  {
    self::getInstance()->addRoute('PUT', $pattern, $handler);
  }

  public static function delete($pattern, $handler)
  {
    self::getInstance()->addRoute('DELETE', $pattern, $handler);
  }
}
