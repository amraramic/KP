<?php


use ra\kp\exceptions\NoValidAccountException;
use PHPUnit\Framework\TestCase;

class NoValidAccountExceptionTest extends TestCase
{

    public function testGetErrorMessage()
    {
        $exception = new NoValidAccountException();

        $this->assertEquals("The account has to be a saving or a checking account!\n", $exception->getErrorMessage());

        $this->expectException(NoValidAccountException::class);
        throw $exception;
    }
}
