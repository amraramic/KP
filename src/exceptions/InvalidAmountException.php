<?php

namespace ra\kp\exceptions;

use Exception;
use Throwable;

class InvalidAmountException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @param float $amount
     * @return string
     */
    public function getDebitErrorMessage(float $amount): string
    {
        return "\nUnfortunately, the account balance isn't sufficient to make the debit of "."$amount"." $"
            . "\nPlease make sure that there is enough money on this account!\n";
    }

    /**
     * @param float $amount
     * @return string
     */
    public function getDepositErrorMessage(float $amount): string
    {
        return "\nYou are trying to deposit an invalid amount (" . $amount . " $) of money to this account."
            . "\nPlease make sure that the amount is greater then zero and smaller than 100 000!\n";
    }


}