<?php

class NotFoundException extends Exception
{
  protected $code    = 404;
  protected $message = 'No se ha encontrado lo que esta buscando.';
}

class UnauthorizedException extends Exception
{
  protected $code    = 403;
  protected $message = 'No tiene los permisos necesarios para realizar esa accion.';
}

class InternalErrorException extends Exception
{
  protected $code    = 500;
  protected $message = 'Ocurrio un error, disculpe las molestias;';
}
