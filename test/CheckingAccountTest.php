<?php

use ra\kp\exceptions\InvalidAccountTypeException;
use ra\kp\exceptions\InvalidAmountException;
use ra\kp\models\CheckingAccount;
use PHPUnit\Framework\TestCase;
use ra\kp\models\Customer;

class CheckingAccountTest extends TestCase
{
    protected CheckingAccount $checkingAccount;
    protected function setUp(): void
    {
        $customer = new Customer(
            "1",
            "Max",
            "Mustermann",
            "Straße 1, 80000 Stadt",
            new DateTime("01.01.1980")
        );

        $this->checkingAccount = new CheckingAccount("100", $customer, 100.0, 0.5);
    }

    /**
     * @throws InvalidAmountException
     */
    public function testAddInterest()
    {
        $this->checkingAccount->setInterestRate(0.0);
        $this->expectException(InvalidAmountException::class);
        $this->checkingAccount->addInterest();

        $this->checkingAccount->setInterestRate(0.05);
        $this->checkingAccount->addInterest();
        $this->assertEquals(100.5, $this->checkingAccount->getBalance());
    }

    /**
     * @throws InvalidAmountException
     */
    public function testDeductAccountMaintenanceCharge()
    {
        $this->checkingAccount->deductAccountMaintenanceCharge();
        $this->assertEquals(93, $this->checkingAccount->getBalance());

        $this->checkingAccount->setBalance(5);
        $this->expectException(InvalidAmountException::class);
        $this->checkingAccount->deductAccountMaintenanceCharge();
    }

    public function testShowInfos(){
        $expected = "100   |  Checking account  |   1    |    Max   |   Mustermann  |  100    |     0.5        |     7\n";
        $this->assertEquals($expected, $this->checkingAccount->showInfos());
    }

    public function testSetInterestRate(){
        $this->assertEquals(0.5, $this->checkingAccount->getInterestRate());
        $this->checkingAccount->setInterestRate(0.6);
        $this->assertEquals(0.6, $this->checkingAccount->getInterestRate());
    }

    public function testGetInterestRate(){
        $this->assertEquals(0.5, $this->checkingAccount->getInterestRate());
    }

    public function testGetAccountMaintenanceCharge(){
        $this->assertEquals(7, $this->checkingAccount->getAccountMaintenanceCharge());
    }
}
