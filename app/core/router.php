<?php

namespace App\Core;

class Router
{
  private static $instance = NULL;

  public $separator = '#';
  public $namespace = 'App\Controllers\\';

  public $routes = [
    'GET'    => [],
    'POST'   => [],
    'PUT'    => [],
    'DELETE' => [],
  ];

  public static function getInstance()
  {
    if (!isset(self::$instance))
    {
      self::$instance = new self();
    }

    return self::$instance;
  }

  public function addRoute($method, $pattern, $handler)
  {
    $route = new Route($pattern, $method, $handler);
    $this->routes[$method][] = $route;
  }

  public function dispatch($request)
  {
    list($controller, $action) = $this->findHandlerFor($request);

    return (new $controller())->execute($action, $request);
  }

  protected function splitHandler($handler)
  {
    return explode($this->separator, $this->namespace . $handler);
  }

  protected function findHandlerFor($request)
  {
    foreach ($this->routes[$request->method] as $route)
    {
      if ($route->matches($request->uri))
      {
        $request->addParams($route->namedParamsFrom($request->uri));

        return $this->splitHandler($route->handler);
      }
    }

    throw new NotFoundException();
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
