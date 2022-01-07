<?php


use ra\kp\exceptions\NoAccountException;
use PHPUnit\Framework\TestCase;

class NoAccountExceptionTest extends TestCase
{
    public function testGetErrorMessage()
    {
        $exception = new NoAccountException();

        $this->assertEquals("An account with this account number does not exists!\n", $exception->getErrorMessage());

        $this->expectException(NoAccountException::class);
        throw $exception;
    }

}
