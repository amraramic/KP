<?php

namespace ra\kp\exceptions;

class InvalidAmountException extends \Exception
{
    /**
     * @param float $amount
     * @param float $balance
     */
    public function getDebitErrorMessage(float $amount, float $balance): string
    {
        return "Unfortunately, your account balance (".$balance." $) isn't sufficient to make the debit (".$amount." $)"
            . "\nPlease make sure that you have enough money on your account!";
    }

    /**
     * @param float $amount
     */
    public function getDepositErrorMessage(float $amount): string
    {
        return "You are trying to deposit an invalid amount (" . $amount . " $) of money to your account."
            . "\nPlease make sure that the amount is greater then zero!";
    }


}