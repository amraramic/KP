<?php

use PHPUnit\Framework\TestCase;
use ra\kp\exceptions\InvalidAmountException;
use ra\kp\models\Customer;
use ra\kp\models\SavingsAccount;

class SavingsAccountTest extends TestCase
{
    protected SavingsAccount $savingsAccount;
    protected function setUp(): void
    {
        $customer = new Customer(
            "1",
            "Max",
            "Mustermann",
            "Straße 1, 80000 Stadt",
            new DateTime("01.01.1980")
        );

        $this->savingsAccount = new SavingsAccount("100", $customer, 100.0, 0.5);
    }

    /**
     * @throws InvalidAmountException
     */
    public function testAddInterest()
    {
        $this->savingsAccount->setInterestRate(0.0);
        $this->expectException(InvalidAmountException::class);
        $this->savingsAccount->addInterest();

        $this->savingsAccount->setInterestRate(0.05);
        $this->savingsAccount->addInterest();
        $this->assertEquals(100.5, $this->savingsAccount->getBalance());
    }

    public function testShowInfos(){
        $expected = "100   |   Savings account  |   1    |    Max   |   Mustermann  |  100     |    0.5\n";
        $this->assertEquals($expected, $this->savingsAccount->showInfos());
    }

    public function testSetInterestRate(){
        $this->assertEquals(0.5, $this->savingsAccount->getInterestRate());
        $this->savingsAccount->setInterestRate(0.6);
        $this->assertEquals(0.6, $this->savingsAccount->getInterestRate());
    }

    public function testGetInterestRate(){
        $this->assertEquals(0.5, $this->savingsAccount->getInterestRate());
    }
}
