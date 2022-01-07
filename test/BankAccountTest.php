<?php


use ra\kp\exceptions\InvalidAmountException;
use ra\kp\models\BankAccount;
use PHPUnit\Framework\TestCase;
use ra\kp\models\Customer;

class BankAccountTest extends TestCase
{
    protected BankAccount $bankAccount;
    protected function setUp(): void
    {
        $customer = new Customer(
            "1",
            "Max",
            "Mustermann",
            "Straße 1, 80000 Stadt",
            new \DateTime("01.01.1980")
        );

        $this->bankAccount = new BankAccount("100", $customer, 100.0);
    }

    /**
     * @throws InvalidAmountException
     */
    public function testDebit()
    {
        $this->bankAccount->debit(30);
        self::assertEquals(70, $this->bankAccount->getBalance());
    }

    /**
     * @throws InvalidAmountException
     */
    public function testDeposit()
    {
        $this->bankAccount->deposit(50);
        self::assertEquals(150, $this->bankAccount->getBalance());
    }
}
