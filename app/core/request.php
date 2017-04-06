<?php

/**
 * Request encapsulates an HTTP request from it's various Globals in PHP.
 *
 *
 * @author Farfanoide <ivan6258@gmail.com>
 */
class Request
{
  /**
   * @var string
   */
  public $uri;

  /**
   * @var string
   */
  public $method;

  /**
   * @var []
   */
  public $params;

  /**
   * @var Request
   */
  protected static $current;

  public function __construct($uri = '/', $method = 'GET', $params = [])
  {
    $this->uri    = $this->sanitizeUri($uri);
    $this->method = $method;
    $this->params = $params;
  }

  /**
   * Returns current request's instance or creates new one.
   *
   * @return Request Current request or new from Super Globals
   */
  public static function current()
  {
    if (!isset(self::$current))
    {
      self::$current = self::fromSuperGlobals();
    }

    return self::$current;
  }

  /**
   * Constructs a Request from PHP SuperGlobals.
   *
   * @return Request A new request
   */
  public static function fromSuperGlobals()
  {
    $instance = new self();

    $instance->uri    = self::sanitizeUri($_SERVER['REQUEST_URI']);
    $instance->method = self::spoofMethod($instance->method);
    $instance->params = self::getParamsFor($instance);

    return $instance;
  }

  /**
   * Removes query string from URI.
   *
   * @return string URI without GET parameters
   */
  public static function sanitizeUri($uri = '')
  {
    return explode('?', $uri)[0];
  }

  /**
   * Support for method spoofing if method is other than GET.
   *
   * @return string
   */
  public static function spoofMethod($method)
  {
    $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : $method;

    if ($method == 'POST')
    {
      $method = isset($_POST['_method']) ? $_POST['_method'] : $method;
    }

    return strtoupper($method);
  }

  /**
   * Check which parameters are required for the request
   * If Request is get, $_GET params are returned, otherwise $_POST.
   *
   * @param Request $request
   *
   * @return []
   */
  public static function getParamsFor($request)
  {
    return $request->isGet() ? $_GET : $_POST;
  }

  public function allParams()
  {
    return array_merge($_GET, $POST);
  }

  /**
   * Merges received params with current ones, giving precedence to the former
   * over the latter.
   *
   * @param [] $params
   */
  public function addParams($params = [])
  {
    $this->params = array_merge($this->params, $params);
  }

  // -----------------------------------------------------------
  // == Helpers
  // -----------------------------------------------------------

  public function isGet()    { return $this->method == 'GET'; }
  public function isPost()   { return $this->method == 'POST'; }
  public function isPut()    { return $this->method == 'PUT'; }
  public function isDelete() { return $this->method == 'DELETE'; }
}
