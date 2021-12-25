<?php

namespace ra\kp\exceptions;

class NoValidAccountException extends \Exception
{
    public function getErrorMessage(): string
    {
        return "The account has to be a saving or a checking account!\n";
    }
}