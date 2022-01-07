<?php


use ra\kp\exceptions\InvalidAmountException;
use PHPUnit\Framework\TestCase;

class InvalidAmountExceptionTest extends TestCase
{

    public function testGetDebitErrorMessage()
    {
        $exception = new InvalidAmountException();
        $this->assertEquals("\nUnfortunately, the account balance isn't sufficient to make the debit of 50 $)\n".
            "Please make sure that there is enough money on this account!\n", $exception->getDebitErrorMessage(50.0, 20.0));

        $this->expectException(InvalidAmountException::class);
        throw $exception;
    }

    public function testGetDepositErrorMessage()
    {
        $exception = new InvalidAmountException();
        $this->assertEquals("\nYou are trying to deposit an invalid amount (20 $) of money to this account.\n".
            "Please make sure that the amount is greater then zero!\n", $exception->getDepositErrorMessage(20.0));

        $this->expectException(InvalidAmountException::class);
        throw $exception;
    }
}
