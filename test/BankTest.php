<?php

use ra\kp\models\Bank;
use ra\kp\models\BankAccount;
use ra\kp\models\Customer;
use PHPUnit\Framework\TestCase;

class BankTest extends TestCase
{
    private Bank $bank;
    private Customer $cust1;
    private Customer $cust2;
    private BankAccount $bankAccount1;
    private BankAccount $bankAccount2;

    protected function setUp(): void
    {
        $this->bank = new Bank();
        $this->cust1 = new Customer(
            "1",
            "Max",
            "Mustermann",
            "Straße 1, 80000 Stadt",
            new \DateTime("01.01.1980")
        );

        $this->cust2 = new Customer(
            "2",
            "Marie",
            "Meier",
            "Straße 2, 80000 Stadt",
            new \DateTime("12.12.1990")
        );

        $this->bankAccount1 = new BankAccount("100", $this->cust1, 100);
        $this->bankAccount2 = new BankAccount("200", $this->cust2, 200);
    }

    public function testCreateNewCustomer()
    {
        $customer = $this->bank->createNewCustomer(
            "Max",
            "Mustermann",
            "Straße 1, 80000 Stadt",
            "01.01.1980");

        $this->assertEquals($this->cust1, $customer);

    }

    public function testGetCustomers()
    {

    }

    public function testDoDebit()
    {

    }

    public function testGetBankAccounts()
    {

    }

    public function testDoTransactionWithAccNumber()
    {

    }

    public function testAddInterest()
    {

    }

    public function testGetBalance()
    {

    }

    public function testDoDeposit()
    {

    }

    public function testDoTransaction()
    {

    }

    public function testCreateNewAccount()
    {

    }
}
