<?php


use ra\kp\exceptions\TransactionFailedException;
use PHPUnit\Framework\TestCase;

class TransactionFailedExceptionTest extends TestCase
{

    public function testGetErrorMessage()
    {
        $exception = new TransactionFailedException();

        $this->assertEquals("Unfortunately, the transaction could not be carried out. Please try again!", $exception->getErrorMessage());

        $this->expectException(TransactionFailedException::class);
        throw $exception;
    }
}
