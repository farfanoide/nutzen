<?php

class Session
{

  public static function start()
  {
    if (session_status() == PHP_SESSION_NONE)
    {
      session_start();
      self::initializeMessages();
    }
  }

  public static function login($user)
  {
    $_SESSION['user'] = $user;
  }

  public static function logout()
  {
    unset($_SESSION['user']);
    session_destroy();
  }

  public static function currentUser()
  {
    return isset($_SESSION['user']) ? $_SESSION['user'] : new User();
  }

  public static function logged()
  {
    return isset($_SESSION['user']);
  }

  public static function addMessage($type, $msg)
  {
    self::initializeMessages();
    $_SESSION['messages'][$type] = array_merge(
      $_SESSION['messages'][$type],
      [$msg]
    );
  }

  public static function getMessages()
  {
    $messages = $_SESSION['messages'];
    unset($_SESSION['messages']);

    return $messages;
  }

  private static function initializeMessages()
  {
    if (!isset($_SESSION['messages']))
    {
      $_SESSION['messages'] = [
        'success' => [],
        'warning' => [],
        'info'    => [],
        'error'   => [],
      ];
    }
  }
}
