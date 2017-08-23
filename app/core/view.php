<?php

class View
{

  public function __construct($template, $context = [], $layout = NULL)
  {
    $this->template = __APP_ROOT__ . "/views/{$template}";
    $this->context  = $context;
    $this->layout   = $layout;
  }

  public function __get($name)
  {
    if (!array_key_exists($name, $this->context))
    {
      $this->context[$name] = "[WARNING] {$name} was not found while rendering {$this->template}";
    }

    return $this->context[$name];
  }

  public function updateContext($context)
  {
    $this->context = $this->mergeContexts($this->context, $context);
  }

  public function render($context = [])
  {
    // First lets update our context with any possible new values so we can be
    // sure they'll be available inside the template
    $this->updateContext($context);

    // We cant echo anything out, otherwise the request gets closed so we
    // render everything within a buffer
    ob_start();

    // by including the template, the code gets executed inside the scope of
    // the object, giving it acces to the object itself as `$this`
    include($this->template);

    // Finally we close the buffer and retrieve its output as a string variable
    $this->rendered_output = ob_get_clean();

    // If invoked with a layout, we call it and we set ourselves the main
    // content while sharing our context with it.
    if ($this->hasLayout())
    {
      $full_context = array_merge($this->context, ['yield' => $this->rendered_output]);
      return (new self($this->layout, $full_context))->render();
    }
    // if there's no layout set, just return our rendered output.
    return $this->rendered_output;
  }

  public function hasLayout()
  {
    return (boolean) $this->layout;
  }

  public function __toString()
  {
    return $this->render();
  }

  public function mergeContexts($context, $otherContext)
  {
    return array_merge($context, $otherContext);
  }

  public function partial($template, $context = [])
  {
    return (new self($template, $this->mergeContexts($this->context, $context)))->render();
  }

}
