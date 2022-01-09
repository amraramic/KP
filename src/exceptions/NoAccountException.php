<?php

namespace ra\kp\exceptions;

use Exception;
use Throwable;

class NoAccountException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getErrorMessage(): string
    {
        return "\nAn account with this account number does not exists!\n";
    }
}