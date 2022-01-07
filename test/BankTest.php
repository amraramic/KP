<?php

use ra\kp\exceptions\InvalidAccountTypeException;
use ra\kp\exceptions\NoAccountException;
use ra\kp\exceptions\InvalidAmountException;
use ra\kp\exceptions\NoCustomerException;
use ra\kp\exceptions\TransactionFailedException;
use ra\kp\models\Bank;
use ra\kp\models\BankAccount;
use ra\kp\models\CheckingAccount;
use ra\kp\models\Customer;
use PHPUnit\Framework\TestCase;
use ra\kp\models\SavingsAccount;

class BankTest extends TestCase
{
    private Bank $bank;
    private Customer $cust1;
    private Customer $cust2;
    private CheckingAccount $bankAccount1;
    private SavingsAccount $bankAccount2;

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

        $this->bankAccount1 = new CheckingAccount($this->bank->generateAccountNumber(), $this->cust1, 100);
        $this->bankAccount2 = new SavingsAccount($this->bank->generateAccountNumber(), $this->cust2, 200);
    }

    public function testCreateNewCustomer()
    {
        $this->expectException(Exception::class);
        $this->bank->createNewCustomer(
            "Max",
            "Mustermann",
            "Straße 1, 80000 Stadt",
            "test");

        $customer = $this->bank->createNewCustomer(
            "Max",
            "Mustermann",
            "Straße 1, 80000 Stadt",
            "01.01.2000");
        $this->assertEquals($this->cust1, $customer);
        $this->expectOutputString("You have created a new customer.Please remember the customer number: 1");
        $this->assertCount(1, $this->bank->getCustomers());
    }

    /**
     * @throws Exception
     */
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
        $this->expectOutputString("You have successfully debit 50 $ from 101 account!\nNew balance: 50 $\n");

        $this->expectException(NoAccountException::class);
        $this->bank->doDebit("200", 100);
    }

    /**
     * @throws TransactionFailedException
     * @throws Exception
     */
    public function testDoTransactionWithAccNumber()
    {
        $this->bank->addBankAccount($this->bankAccount1);
        $this->bank->addBankAccount($this->bankAccount2);

        $this->expectException(TransactionFailedException::class);
        $this->bank->doTransactionWithAccNumber("300", "200", 20.0);

        $this->expectException(TransactionFailedException::class);
        $this->bank->doTransactionWithAccNumber("200", "200", 20.0);

        $this->expectOutputString(
            "You have successfully debit 20 $ from 101 account!\n".
            "New balance: 80 $\n" .
            "You have successfully deposit 20 $ to 102 account!\n" .
            "New balance: 220 $\n");
        $this->assertTrue($this->bank->doTransactionWithAccNumber("101", "200", 20.0));
    }

    /**
     * @throws NoAccountException
     */
    public function testGetBalance()
    {
        $this->bank->addCustomer($this->cust1);
        $this->bank->addBankAccount($this->bankAccount1);
        $accNumber = $this->bankAccount1->getAccountNumber();
        $this->assertEquals(100.0, $this->bank->getBalance($accNumber));

        $this->expectException(NoAccountException::class);
        $this->bank->getBalance("300");

    }

    /**
     * @throws InvalidAmountException
     * @throws NoAccountException
     */
    public function testDoDeposit()
    {
        $this->bank->addCustomer($this->cust1);
        $this->bank->addBankAccount($this->bankAccount1);

        $this->assertEquals(250.0, $this->bank->doDeposit("101", 50.0));
        $this->expectOutputString("You have successfully deposit 50 $ to 101 account!\nNew balance: 150 $\n");

        $this->expectException(NoAccountException::class);
        $this->bank->doDeposit("200", 100);
    }

    /**
     * @throws TransactionFailedException
     * @throws InvalidAmountException
     */
    public function testDoTransaction()
    {
        $this->bank->addCustomer($this->cust1);
        $this->bank->addBankAccount($this->bankAccount1);
        $this->bank->addCustomer($this->cust2);
        $this->bank->addBankAccount($this->bankAccount2);

        $this->expectOutputString(
            "You have successfully debit 20 $ from 101 account!\n".
            "New balance: 80 $\n" .
            "You have successfully deposit 20 $ to 102 account!\n" .
            "New balance: 220 $\n");
        $this->assertTrue($this->bank->doTransaction($this->bankAccount1, $this->bankAccount2, 20.0));

        $this->expectException(TransactionFailedException::class);
        $this->bank->doTransaction($this->bankAccount1, $this->bankAccount2, 0);
        $this->bank->doTransaction($this->bankAccount1, $this->bankAccount2, 150);

    }

    /**
     * @throws NoCustomerException|InvalidAccountTypeException
     */
    public function testCreateNewAccount()
    {
        $this->bank->addCustomer($this->cust1);
        $expect = new CheckingAccount("105", $this->cust1, 100);
        $bankAccount = $this->bank->createNewAccount(
            $this->cust1->getCustomerNumber(),
            "c",
            100.0
        );

        $this->assertEquals($expect, $bankAccount);
        $this->expectOutputString("You have created a new bank account. Please remember the account number: 105");
        $this->assertCount(1, $this->bank->getCheckingAccounts());

        $this->expectException(NoCustomerException::class);
        $this->bank->createNewAccount(5, "", 100.0);

        $this->expectException(InvalidAccountTypeException::class);
        $this->bank->createNewAccount(
            $this->cust1->getCustomerNumber(),
            "d",
            100.0
        );
    }
}
