<?php

class View
{

  public function __construct($template, $context, $layout = NULL)
  {
    $this->template = __APP_ROOT__ . "/views/{$template}";
    $this->context  = $context;
    $this->layout   = $layout;
  }

  public function __get($name)
  {
    if (!array_key_exists($name, $this->context))
    {
      $this->context[$name] = "[WARNIGN] {$name} was not found while rendering {$this->template}";
    }

    return $this->context[$name];
  }

  public function render($context = [])
  {
    ob_start();
    include($this->template);
    $this->final = ob_get_clean();

    if ($this->hasLayout())
    {
      $full_context = array_merge($this->context, $context, ['content' => $this->final]);
      return (new self($this->layout, $full_context))->render();
    }
    return $this->final;
  }

  public function hasLayout()
  {
    return (boolean) $this->layout;
  }

  public function __toString()
  {
    return $this->render();
  }

  public function partial($template, $context=[])
  {
    return (new self($template, $context))->render();
  }
}
