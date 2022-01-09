<?php

namespace ra\kp\exceptions;

use Exception;
use Throwable;

class InvalidAccountTypeException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getErrorMessage(): string
    {
        return "\nPlease chose between savings (S) or checking (C) account\n";
    }
}