<?php

namespace ra\kp\exceptions;

use Exception;
use Throwable;

class NoValidAccountException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getErrorMessage(): string
    {
        return "\nThe account has to be a saving or a checking account!\n";
    }

    public function getCheckingAccountErrorMessage(): string
    {
        return "\nThe account has to be a checking account!\n";
    }
}