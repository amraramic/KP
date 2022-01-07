<?php

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
            new \DateTime("01.01.1980")
        );

        $this->checkingAccount = new CheckingAccount("100", $customer, 100.0, 0.5);
    }

    public function testAddInterest()
    {
        $this->checkingAccount->addInterest();
        $this->assertEquals(100.5, $this->checkingAccount->getBalance());
    }

    public function testDeductAccountMaintenanceCharge()
    {
        $this->checkingAccount->deductAccountMaintenanceCharge();
        $this->assertEquals(93, $this->checkingAccount->getBalance());
    }
}
