<?php

namespace ra\kp\exceptions;

class InvalidAccountTypeException extends \Exception
{
    public function getErrorMessage(): string
    {
        return "Please chose between savings (S) or checking (C) account\n";
    }
}