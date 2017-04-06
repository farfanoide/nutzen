<?php

require_once __APP_ROOT__ . '/core/controller.php';
require_once __APP_ROOT__ . '/core/view.php';

class ApplicationController extends Controller
{

  public function home($request)
  {
    $this->response->content = (new View('test.html', ['mire' => 'no se puede creeer'], 'layout.html'))->render();
  }

}
