<?php

use PHPUnit\Framework\TestCase;
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
            new \DateTime("01.01.1980")
        );

        $this->savingsAccount = new SavingsAccount("100", $customer, 100.0, 0.5);
    }
    public function testAddInterest()
    {
        $this->savingsAccount->addInterest();
        $this->assertEquals(100.5, $this->savingsAccount->getBalance());
    }
}
