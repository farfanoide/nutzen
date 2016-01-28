<?php

namespace App\Core;

class Engine
{

  protected $suffix;
  protected $environment;

  public function __construct($suffix = '.html.twig')
  {
    $this->suffix = $suffix;
    $this->environment = new \Twig_Environment(
      new \Twig_Loader_Filesystem(root() . '/views')
    );
  }

  public function render($template, $context)
  {
    return $this->environment->render($template . $this->suffix, $context);
  }

}
