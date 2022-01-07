<?php

use ra\kp\exceptions\NoAccountException;
use ra\kp\exceptions\InvalidAmountException;
use ra\kp\exceptions\NoCustomerException;
use ra\kp\exceptions\TransactionFailedException;
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

        $this->bankAccount1 = new BankAccount("101", $this->cust1, 100);
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
        $this->expectOutputString("You have created a new customer.Please remember the customer number: 1");
        $this->assertCount(1, $this->bank->getCustomers());
    }

    public function testGetCustomers()
    {
        $customer1 = $this->bank->createNewCustomer(
            "Max",
            "Mustermann",
            "Straße 1, 80000 Stadt",
            "01.01.1980");
        $this->bank->createNewCustomer(
            "Marie",
            "Meier",
            "Straße 2, 90000 Stadt",
            "12.12.1960");

        $customers = $this->bank->getCustomers();
        $this->assertCount(2, $customers);
        $this->assertEquals($customer1, reset($customers));

    }

    /**
     * @throws NoAccountException
     * @throws InvalidAmountException
     */
    public function testDoDebit()
    {
        $this->bank->addCustomer($this->cust1);
        $this->bank->addBankAccount($this->bankAccount1);

        $this->assertEquals(150.0, $this->bank->doDebit("101", 50.0));
        $this->expectOutputString("You have successfully debit 50 $ to 101 account!\nNew balance: 50\n");
    }

    public function testGetBankAccounts(){
        $this->bank->addCustomer($this->cust1);
        $bankAccount1 = $this->bank->addBankAccount($this->bankAccount1);
        $this->bank->addCustomer($this->cust2);
        $this->bank->addBankAccount($this->bankAccount2);

        $bankAccounts = $this->bank->getBankAccounts();
        $this->assertCount(2, $bankAccounts);
        $this->assertEquals($bankAccount1, reset($bankAccounts));

    }

    /**
     * @throws TransactionFailedException
     * @throws Exception
     */
    public function testDoTransactionWithAccNumber()
    {
        $this->bank->addBankAccount($this->bankAccount1);
        $this->bank->addBankAccount($this->bankAccount2);

        $this->assertTrue($this->bank->doTransactionWithAccNumber("101", "200", 20.0));
        $this->expectOutputString(
            "You have successfully debit 20 $ to 101 account!\n".
            "New balance: 80\n" .
            "You have successfully deposit 20 $ to 200 account!\n" .
            "New balance: 220\n");
    }

    public function testGetBalance()
    {
        $this->bank->addCustomer($this->cust1);
        $this->bank->addBankAccount($this->bankAccount1);
        $accNumber = $this->bankAccount1->getAccountNumber();
        $this->assertEquals(100.0, $this->bank->getBalance($accNumber));

    }

    public function testDoDeposit()
    {
        $this->bank->addCustomer($this->cust1);
        $this->bank->addBankAccount($this->bankAccount1);

        $this->assertEquals(250.0, $this->bank->doDeposit("101", 50.0));
        $this->expectOutputString("You have successfully deposit 50 $ to 101 account!\nNew balance: 150\n");
    }

    /**
     * @throws TransactionFailedException
     */
    public function testDoTransaction()
    {
        $this->bank->addCustomer($this->cust1);
        $this->bank->addBankAccount($this->bankAccount1);
        $this->bank->addCustomer($this->cust2);
        $this->bank->addBankAccount($this->bankAccount2);

        $this->assertTrue($this->bank->doTransaction($this->bankAccount1, $this->bankAccount2, 20.0));
        $this->expectOutputString(
            "You have successfully debit 20 $ to 101 account!\n".
            "New balance: 80\n" .
            "You have successfully deposit 20 $ to 200 account!\n" .
            "New balance: 220\n");
    }

    /**
     * @throws NoCustomerException
     */
    public function testCreateNewAccount()
    {
        $this->bank->addCustomer($this->cust1);
        $bankAccount = $this->bank->createNewAccount(
            $this->cust1->getCustomerNumber(),
            "",
            100.0
        );

        $this->assertEquals($this->bankAccount1, $bankAccount);
        $this->expectOutputString("You have created a new bank account. Please remember the account number: 101");
        $this->assertCount(1, $this->bank->getBankAccounts());
    }
}
