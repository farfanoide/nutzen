<?php

// ----------------------------------------------
// == Global Helper Functions
// ----------------------------------------------

function root()
{
  return __ROOT__;
}

function app_root()
{
  return __APP_ROOT__;
}

function request()
{
  return Request::current();
}

function current_user()
{
  return Session::currentUser();
}

function partial($name, $context=[])
{
  return (new Sarasa($name . '.html', $context))->render();
}
