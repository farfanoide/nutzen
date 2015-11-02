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
  return root() . '/app';
}

function request()
{
  return Request::current();
}

function current_user()
{
  return Session::currentUser();
}
