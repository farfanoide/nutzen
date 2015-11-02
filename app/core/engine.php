<?php

namespace App\Core;

class Engine
{

  protected $suffix  = '.html.twig';

  public function __construct()
  {
    $this->environment = new \Twig_Environment(
      new \Twig_Loader_Filesystem(root() . '/views')
    );
  }

  public function render($template, $context)
  {
    return $this->environment->render($template . $this->suffix, $context);
  }

}
