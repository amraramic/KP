<?php

namespace ra\kp\exceptions;

use Exception;
use Throwable;

class NoCustomerException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getErrorMessage(): string
    {
        return "\nA customer with this customer number does not exists!\n";
    }
}