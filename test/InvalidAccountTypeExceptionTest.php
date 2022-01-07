<?php


use ra\kp\exceptions\InvalidAccountTypeException;
use PHPUnit\Framework\TestCase;

class InvalidAccountTypeExceptionTest extends TestCase
{

    public function testGetErrorMessage()
    {
        $exception = new InvalidAccountTypeException();
        $this->assertEquals("\nPlease chose between savings (S) or checking (C) account\n", $exception->getErrorMessage());

        $this->expectException(InvalidAccountTypeException::class);
        throw $exception;
    }
}
