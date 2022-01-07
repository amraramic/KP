<?php

namespace ra\kp\exceptions;

class NoCustomerException extends \Exception
{
    public function getErrorMessage(): string
    {
        return "\nA customer with this customer number does not exists!\n";
    }
}