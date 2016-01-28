<?php

namespace App\Core;

use App\Core\Engine as Engine;

class View
{

  protected $context;
  protected $engine;

  function __construct($template, $context = [], $engine = NULL)
  {
    $this->template = $template;
    $this->context  = $context;
    $this->engine   = $engine ?: new Engine();
  }

  public function render($context = [])
  {
    return $this->engine->render(
      $this->template,
      array_merge($this->context, $context)
    );
  }

  public function __toString()
  {
    return $this->render();
  }
}
