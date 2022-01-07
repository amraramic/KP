<?php

namespace ra\kp\exceptions;

class TransactionFailedException extends \Exception
{
    public function getErrorMessage(): string
    {
        return "\nUnfortunately, the transaction could not be carried out. Please try again!\n";
    }
}