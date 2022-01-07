<?php


use ra\kp\exceptions\InvalidAmountException;
use PHPUnit\Framework\TestCase;

class InvalidAmountExceptionTest extends TestCase
{

    public function testGetDebitErrorMessage()
    {
        $exception = new InvalidAmountException();
        $this->assertEquals("Unfortunately, your account balance (20 $) isn't sufficient to make the debit (50 $)"
            . "\nPlease make sure that you have enough money on your account!", $exception->getDebitErrorMessage(50.0, 20.0));

        $this->expectException(InvalidAmountException::class);
        throw $exception;
    }

    public function testGetDepositErrorMessage()
    {
        $exception = new InvalidAmountException();
        $this->assertEquals("You are trying to deposit an invalid amount (20 $) of money to your account."
            . "\nPlease make sure that the amount is greater then zero!", $exception->getDepositErrorMessage(20.0));

        $this->expectException(InvalidAmountException::class);
        throw $exception;
    }
}
