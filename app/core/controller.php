<?php

require_once __APP_ROOT__ . '/core/session.php';
require_once __APP_ROOT__ . '/core/response.php';
require_once __APP_ROOT__ . '/core/view.php';

class Controller
{

  public function __construct()
  {
    $this->response = new Response();
  }

  public function redirect($url, $statusCode = 302)
  {
    $this->response->withHeader('Location', $url)->withStatus($statusCode);
    return $this;
  }

  public function view($template, $params = [])
  {
    $this->response->content = (new View($template, $params))->render();
  }

  public function withMessage($type, $message)
  {
    Session::addMessage($type, $message);
    return $this;
  }

  public function execute($action, $request)
  {
    $this->$action($request);
    return $this->response;
  }

}

