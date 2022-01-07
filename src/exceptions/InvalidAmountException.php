<?php

namespace ra\kp\exceptions;

class InvalidAmountException extends \Exception
{
    /**
     * @param float $amount
     * @return string
     */
    public function getDebitErrorMessage(float $amount): string
    {
        return "Unfortunately, the account balance isn't sufficient to make the debit of "."$amount"." $)"
            . "\nPlease make sure that there is enough money on this account!\n";
    }

    /**
     * @param float $amount
     * @return string
     */
    public function getDepositErrorMessage(float $amount): string
    {
        return "You are trying to deposit an invalid amount (" . $amount . " $) of money to this account."
            . "\nPlease make sure that the amount is greater then zero!\n";
    }


}