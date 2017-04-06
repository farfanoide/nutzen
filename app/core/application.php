<?php

require_once realpath(__APP_ROOT__ . '/core/view.php');
require_once realpath(__APP_ROOT__ . '/core/response.php');

class Application
{

  public $request;
  protected $env;
  protected $router;

  /**
   * @param string $environment
   */
  public function __construct($environment = 'development')
  {
    $this->env    = $environment;
    $this->router = Router::getInstance();
    $this->setLogger();
  }

  /**
   * @param null $request
   *
   * @return Response
   */
  public function run($request = NULL)
  {
    $this->request = $request ?: Request::current();

    try {

      return $this->router->dispatch($this->request);

    } catch (\Exception $exception) {

      return $this->handleException($exception);

    }
  }

  /**
   *
   * @param $exception
   *
   * @return Response
   *
   */
  protected function handleException($exception)
  {
    $view = new View('error.html', [
      'code' => $exception->getCode(),
      'message' => $exception->getMessage()
    ]);

    return new Response($view->render(), $exception->getCode());
  }

  protected function setLogger()
  {
    if ($this->env == 'development')
    {
      ini_set('display_startup_errors', 1);
      ini_set('display_errors', 1);
      error_reporting(-1);
    }
  }
}
