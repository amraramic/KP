<?php

namespace ra\kp\exceptions;

use Exception;
use Throwable;

class TransactionFailedException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getErrorMessage(): string
    {
        return "\nUnfortunately, the transaction could not be carried out. Please try again!\n";
    }
}