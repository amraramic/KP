<?php

namespace ra\kp\exceptions;

use Exception;

class NoAccountException extends Exception
{
    public function getErrorMessage(): string
    {
        return "An account with this account number does not exists!\n";
    }
}