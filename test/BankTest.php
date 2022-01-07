<?php

use ra\kp\exceptions\InvalidAccountTypeException;
use ra\kp\exceptions\NoAccountException;
use ra\kp\exceptions\InvalidAmountException;
use ra\kp\exceptions\NoCustomerException;
use ra\kp\exceptions\NoValidAccountException;
use ra\kp\exceptions\TransactionFailedException;
use ra\kp\models\Bank;
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
            1,
            "Max",
            "Mustermann"
        );

        $this->cust2 = new Customer(
            2,
            "Marie",
            "Meier"
        );

        $this->bankAccount1 = new CheckingAccount("101", $this->cust1, 100);
        $this->bankAccount2 = new SavingsAccount("102", $this->cust2, 200);
    }

    public function testCreateNewCustomer()
    {
        $customer = $this->bank->createNewCustomer(
            "Max",
            "Mustermann"
        );
        $this->assertEquals($this->cust1, $customer);
        $this->expectOutputString("You have created a new customer.Please remember the customer number: 1");
        $this->assertCount(1, $this->bank->getCustomers());
    }

    public function testGetCustomers()
    {
        $customer1 = $this->bank->createNewCustomer(
            "Max",
            "Mustermann"
        );
        $this->bank->createNewCustomer(
            "Marie",
            "Meier"
        );

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
        $bankAccount = $this->bank->createNewAccount(
            $this->cust1->getCustomerNumber(),
            "c",
            100.0
        );

        $this->assertEquals($this->bankAccount1, $bankAccount);
        $this->expectOutputString("You have created a new bank account. Please remember the account number: 101");
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

    public function testAddCustomer()
    {
        $this->assertCount(0,$this->bank->getCustomers());
        $this->bank->addCustomer($this->cust1);
        $this->assertCount(1, $this->bank->getCustomers());
    }

    public function testAddBankAccount()
    {
        $this->assertCount(0,$this->bank->getCheckingAccounts());
        $this->bank->addBankAccount($this->bankAccount1);
        $this->assertCount(1, $this->bank->getCheckingAccounts());

        $this->assertCount(0,$this->bank->getSavingsAccounts());
        $this->bank->addBankAccount($this->bankAccount2);
        $this->assertCount(1, $this->bank->getSavingsAccounts());
    }

    /**
     * @throws NoCustomerException
     */
    public function testDeleteCustomer(){
        $this->bank->addCustomer($this->cust1);
        $this->assertArrayHasKey(1, $this->bank->getCustomers());
        $this->bank->deleteCustomer(1);
        $this->assertArrayNotHasKey(1, $this->bank->getCustomers());

        $this->expectException(NoCustomerException::class);
        $this->bank->deleteCustomer(10);
    }

    /**
     * @throws NoAccountException
     */
    public function testDeleteAccount(){
        $this->bank->addBankAccount($this->bankAccount1);
        $this->assertArrayHasKey("101", $this->bank->getCheckingAccounts());
        $this->bank->deleteAccount("101");
        $this->assertArrayNotHasKey("101", $this->bank->getCustomers());

        $this->expectException(NoAccountException::class);
        $this->bank->deleteAccount("200");
    }

    /**
     * @throws InvalidAmountException
     * @throws NoAccountException
     */
    public function testAddInterest()
    {
        $this->bank->addBankAccount($this->bankAccount1);
        $this->expectException(NoAccountException::class);
        $this->bank->addInterest("105");
    }

    /**
     * @throws NoAccountException
     */
    public function testDeductMaintenanceCharge(){
        $this->bank->addBankAccount($this->bankAccount2);

        $this->expectException(NoValidAccountException::class);
        $this->bank->deductMaintenanceCharge("102");

        $this->expectException(NoAccountException::class);
        $this->bank->deductMaintenanceCharge("103");
    }

    public function testGenerateAccountNumber(){
        $this->assertEquals("101", $this->bank->generateAccountNumber());
    }

    public function testGenerateCustomerNumber(){
        $this->assertEquals("1", $this->bank->generateCustomerNumber());
    }
}
