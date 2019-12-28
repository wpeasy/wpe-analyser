<?php


namespace WPEasyLibrary\Exceptions;


use Throwable;

class ModuleNotExistException extends \Exception
{
    public function __construct($message = "Module does not exist", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}