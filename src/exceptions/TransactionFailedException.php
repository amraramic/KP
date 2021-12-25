<?php

namespace ra\kp\exceptions;

class TransactionFailedException extends \Exception
{
    /**
     * @param float $amount
     * @param float $balance
     */
    public function getErrorMessage(): string
    {
        return "Unfortunately, the transaction could not be carried out. Please try again!";
    }
}