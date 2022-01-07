<?php

namespace ra\kp\exceptions;

class NoValidAccountException extends \Exception
{
    public function getErrorMessage(): string
    {
        return "\nThe account has to be a saving or a checking account!\n";
    }

    public function getCheckingAccountErrorMessage(): string
    {
        return "\nThe account has to be a checking account!\n";
    }
}