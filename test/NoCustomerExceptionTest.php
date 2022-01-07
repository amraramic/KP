<?php


use ra\kp\exceptions\NoCustomerException;
use PHPUnit\Framework\TestCase;

class NoCustomerExceptionTest extends TestCase
{

    public function testGetErrorMessage()
    {
        $exception = new NoCustomerException();

        $this->assertEquals("A customer with this customer number does not exists!\n", $exception->getErrorMessage());

        $this->expectException(NoCustomerException::class);
        throw $exception;
    }
}
