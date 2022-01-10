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
    private CheckingAccount $bankAccount3;
    private SavingsAccount $bankAccount4;

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
        $this->bankAccount3 = new CheckingAccount("103", $this->cust2, 50);
        $this->bankAccount4 = new SavingsAccount("104", $this->cust1, 150);
    }

    public function testCreateNewCustomer()
    {
        $this->assertEquals($this->cust1,
            $this->bank->createNewCustomer(
                "Max",
                "Mustermann"));
        $this->expectOutputString("You have created a new customer.Please remember the customer number: 1\n");
        $this->assertCount(1, $this->bank->getCustomers());
    }

    public function testGetCustomers()
    {
        $customers = $this->bank->getCustomers();
        $this->assertCount(0, $customers);

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
     * @throws InvalidAmountException
     */
    public function testDoDebit()
    {
        $this->bank->addBankAccount($this->bankAccount1);
        $this->assertEquals(150.0, $this->bank->doDebit("101", 50.0));
        $this->bank->addBankAccount($this->bankAccount2);
        $this->assertEquals(150.0, $this->bank->doDebit("102", 50.0));
        $this->expectOutputString("You have successfully debit 50 $ from 101 account!\nNew balance: 50 $\n".
            "You have successfully debit 50 $ from 102 account!\nNew balance: 150 $\n");

        $this->expectException(InvalidAmountException::class);
        $this->assertFalse($this->bank->doDebit("101", 100.0));
    }

    /**
     * @throws TransactionFailedException
     * @throws Exception
     */
    public function testDoTransactionWithAccNumber()
    {
        $this->bank->addBankAccount($this->bankAccount1);
        $this->bank->addBankAccount($this->bankAccount2);

        $this->assertTrue($this->bank->doTransactionWithAccNumber("101", "102", 20.0));
        $this->assertTrue($this->bank->doTransactionWithAccNumber("102", "101", 20.0));


        $this->expectException(TransactionFailedException::class);
        $this->assertFalse($this->bank->doTransactionWithAccNumber("300", "200", 20.0));
        $this->assertFalse($this->bank->doTransactionWithAccNumber("200", "200", 20.0));
        $this->assertFalse($this->bank->doTransactionWithAccNumber("101", "101", 20.0));

        $this->expectOutputString(
            "You have successfully debit 20 $ from 101 account!\n".
            "New balance: 80 $\n" .
            "You have successfully deposit 20 $ to 102 account!\n" .
            "New balance: 220 $\n");
    }

    public function testGetBalance()
    {
        $this->bank->addBankAccount($this->bankAccount1);
        $accNumber = $this->bankAccount1->getAccountNumber();
        $this->assertEquals(100.0, $this->bank->getBalance($accNumber));

        $this->bank->addBankAccount($this->bankAccount2);
        $accNumber = $this->bankAccount2->getAccountNumber();
        $this->assertEquals(200.0, $this->bank->getBalance($accNumber));

        $this->assertEquals(0.0, $this->bank->getBalance("103"));
    }

    /**
     * @throws InvalidAmountException
     */
    public function testDoDeposit()
    {
        $this->bank->addBankAccount($this->bankAccount1);
        $this->bank->addBankAccount($this->bankAccount2);

        $this->assertEquals(150.0, $this->bank->doDeposit("101", 50.0));
        $this->assertEquals(250.0, $this->bank->doDeposit("102", 50.0));
        $this->expectOutputString("You have successfully deposit 50 $ to 101 account!\nNew balance: 150 $\n".
            "You have successfully deposit 50 $ to 102 account!\nNew balance: 250 $\n");

        $this->expectException(InvalidAmountException::class);
        $this->assertFalse($this->bank->doDeposit("101", 0.0));
    }

    /**
     * @throws TransactionFailedException
     * @throws InvalidAmountException
     */
    public function testDoTransaction()
    {
        $this->bank->addBankAccount($this->bankAccount1);
        $this->bank->addBankAccount($this->bankAccount2);

        $this->expectOutputString(
            "You have successfully debit 20 $ from 101 account!\n".
            "New balance: 80 $\n" .
            "You have successfully deposit 20 $ to 102 account!\n" .
            "New balance: 220 $\n");
        $this->assertTrue($this->bank->doTransaction($this->bankAccount1, $this->bankAccount2, 20.0));

        $this->expectException(TransactionFailedException::class);
        $this->assertFalse($this->bank->doTransaction($this->bankAccount1, $this->bankAccount2, 0));
        $this->assertFalse($this->bank->doTransaction($this->bankAccount1, $this->bankAccount2, 150));

    }

    /**
     * @throws InvalidAccountTypeException
     */
    public function testCreateNewAccount()
    {
        $this->bank->addCustomer($this->cust1);
        $this->bank->addCustomer($this->cust2);

        $this->assertEquals($this->bankAccount1, $this->bank->createNewAccount(
            $this->cust1->getCustomerNumber(),
            "C",
            100.0
        ));
        $this->assertCount(1, $this->bank->getCheckingAccounts());

        $this->assertEquals($this->bankAccount2, $this->bank->createNewAccount(
            $this->cust2->getCustomerNumber(),
            "S",
            200.0
        ));
        $this->assertCount(1, $this->bank->getSavingsAccounts());

        $this->assertEquals($this->bankAccount3, $this->bank->createNewAccount(
            $this->cust2->getCustomerNumber(),
            "c",
            50.0
        ));
        $this->assertCount(2, $this->bank->getCheckingAccounts());

        $this->assertEquals($this->bankAccount4, $this->bank->createNewAccount(
            $this->cust1->getCustomerNumber(),
            "s",
            150.0
        ));
        $this->assertCount(2, $this->bank->getSavingsAccounts());

        $this->expectOutputString("You have created a new bank account. Please remember the account number: 101\n".
                "You have created a new bank account. Please remember the account number: 102\n".
                "You have created a new bank account. Please remember the account number: 103\n".
                "You have created a new bank account. Please remember the account number: 104\n");

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
        $this->assertEquals($this->cust1, $this->bank->addCustomer($this->cust1));
        $this->assertCount(1, $this->bank->getCustomers());
    }

    public function testAddBankAccount()
    {
        $this->assertCount(0,$this->bank->getCheckingAccounts());
        $this->assertEquals($this->bankAccount1, $this->bank->addBankAccount($this->bankAccount1));
        $this->assertCount(1, $this->bank->getCheckingAccounts());

        $this->assertCount(0,$this->bank->getSavingsAccounts());
        $this->assertEquals($this->bankAccount2, $this->bank->addBankAccount($this->bankAccount2));
        $this->assertCount(1, $this->bank->getSavingsAccounts());
    }

    public function testDeleteCustomer(){
        $this->bank->addCustomer($this->cust1);
        $this->bank->addBankAccount($this->bankAccount1);
        $this->bank->addBankAccount($this->bankAccount4);

        $this->assertArrayHasKey(1, $this->bank->getCustomers());
        $this->assertArrayHasKey("101", $this->bank->getCheckingAccounts());
        $this->assertArrayHasKey("104", $this->bank->getSavingsAccounts());
        $this->bank->deleteCustomer(1);

        $this->assertArrayNotHasKey("101", $this->bank->getCheckingAccounts());
        $this->assertArrayNotHasKey("104", $this->bank->getSavingsAccounts());
        $this->assertArrayNotHasKey(1, $this->bank->getCustomers());
    }

    public function testDeleteAccount(){
        $this->bank->addBankAccount($this->bankAccount1);
        $this->assertArrayHasKey("101", $this->bank->getCheckingAccounts());
        $this->bank->deleteAccount("101");
        $this->assertArrayNotHasKey("101", $this->bank->getCustomers());

        $this->bank->addBankAccount($this->bankAccount2);
        $this->assertArrayHasKey("102", $this->bank->getSavingsAccounts());
        $this->bank->deleteAccount("102");
        $this->assertArrayNotHasKey("102", $this->bank->getCustomers());
    }

    /**
     * @throws InvalidAmountException
     */
    public function testAddInterest()
    {
        $this->bank->addBankAccount($this->bankAccount1);

        $this->bankAccount1->setInterestRate(0.05);
        $result = $this->bank->addInterest("101");
        $this->assertEquals(100.05, $result);

        $this->bank->addBankAccount($this->bankAccount2);
        $this->bankAccount2->setInterestRate(0.01);
        $result = $this->bank->addInterest("102");
        $this->assertEquals(200.02, $result);

        $this->assertEquals(0.0, $this->bank->addInterest("103"));

        $this->bankAccount1->setInterestRate(0.0);
        $this->expectException(InvalidAmountException::class);
        $this->assertEquals(0.0, $this->bank->addInterest("101"));
    }


    /**
     * @throws NoValidAccountException
     */
    public function testDeductMaintenanceCharge(){
        $this->bank->addBankAccount($this->bankAccount1);
        $this->bank->addBankAccount($this->bankAccount2);

        $this->assertEquals(93.0, $this->bank->deductMaintenanceCharge("101"));

        $this->bankAccount1->setBalance(5);
        $this->expectOutputString("You have successfully debit 7 $ from 101 account!\n".
            "New balance: 93 $\n".
            "\nUnfortunately, the account balance isn't sufficient to make the debit of 7 $\n".
            "Please make sure that there is enough money on this account!\n");
        $this->bank->deductMaintenanceCharge("101");

        $this->expectException(NoValidAccountException::class);
        $this->bank->deductMaintenanceCharge("102");

    }

    public function testGenerateAccountNumber(){
        $this->assertEquals("101", $this->bank->generateAccountNumber());
    }

    public function testGenerateCustomerNumber(){
        $this->assertEquals("1", $this->bank->generateCustomerNumber());
    }

    /**
     * @throws NoCustomerException
     */
    public function testCheckCustomerNumber(){
        $this->bank->addCustomer($this->cust1);

        $this->assertTrue($this->bank->checkCustomerNumber(1));
        $this->expectException(NoCustomerException::class);
        $this->assertFalse($this->bank->checkCustomerNumber(2));
    }

    /**
     * @throws NoAccountException
     */
    public function testCheckAccountNumber(){
        $this->bank->addBankAccount($this->bankAccount1);
        $this->bank->addBankAccount($this->bankAccount2);

        $this->assertTrue($this->bank->checkAccountNumber("101"));
        $this->assertTrue($this->bank->checkAccountNumber("102"));
        $this->expectException(NoAccountException::class);
        $this->assertFalse($this->bank->checkAccountNumber("100"));
    }

    public function testGetCheckingAccounts(){
        $expected = ["101" => $this->bankAccount1];

        $this->assertEmpty($this->bank->getCheckingAccounts());
        $this->bank->addBankAccount($this->bankAccount1);
        $this->assertEquals($expected, $this->bank->getCheckingAccounts());
    }

    public function testGetSavingAccounts(){
        $expected = ["102" => $this->bankAccount2];

        $this->assertEmpty($this->bank->getSavingsAccounts());
        $this->bank->addBankAccount($this->bankAccount2);
        $this->assertEquals($expected, $this->bank->getSavingsAccounts());
    }
}
