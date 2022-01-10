<?php
namespace ra\kp\models;

use ra\kp\exceptions\InvalidAccountTypeException;
use ra\kp\exceptions\NoAccountException;
use ra\kp\exceptions\NoCustomerException;
use ra\kp\exceptions\NoValidAccountException;
use ra\kp\exceptions\InvalidAmountException;
use ra\kp\exceptions\TransactionFailedException;
use ra\kp\interfaces\Banking;

class Bank implements Banking
{
    /** @var int */
    private int $customerNumberGen;

    /** @var int */
    private int $accountNumberGen;

    /** @var Customer[] */
    private array $customers;

    /** @var SavingsAccount[] */
    private array $savingsAccounts;

    /** @var CheckingAccount[] */
    private array $checkingAccounts;

    public function __construct()
    {
        $this->accountNumberGen = 100;
        $this->customerNumberGen = 0;
        $this->customers = [];
        $this->savingsAccounts = [];
        $this->checkingAccounts = [];
    }


    /**
     * @param BankAccount $from
     * @param BankAccount $to
     * @param float $amount
     * @return bool
     * @throws TransactionFailedException|InvalidAmountException
     */
    public function doTransaction(BankAccount $from, BankAccount $to, float $amount) : bool{
        if ($amount > 0 && $from->getBalance() >= $amount) {
            $from->debit($amount);
            $to->deposit($amount);
            return true;
        } else {
            throw new TransactionFailedException("Make sure that the amount is greater than 0 and that there is enough money to be debited!\n");
        }
    }

    /**
     * @param string $from
     * @param string $to
     * @param float $amount
     * @return bool
     * @throws TransactionFailedException
     * @throws InvalidAmountException
     */
    public function doTransactionWithAccNumber(string $from, string $to, float $amount) : bool{
        $toAccount = null;
        $fromAccount = null;
        if(key_exists($to, $this->checkingAccounts)){
            $toAccount = $this->checkingAccounts[$to];
        } elseif (key_exists($to, $this->savingsAccounts)) {
            $toAccount = $this->savingsAccounts[$to];
        }
        if(key_exists($from, $this->checkingAccounts)){
            $fromAccount = $this->checkingAccounts[$from];
        } elseif (key_exists($from, $this->savingsAccounts)) {
            $fromAccount = $this->savingsAccounts[$from];
        }
        if(($from != $to) && ($fromAccount && $toAccount)){
            return $this->doTransaction($fromAccount, $toAccount, $amount);
        } else {
            throw new TransactionFailedException("The account numbers are invalid!\n");
        }
    }

    function createNewCustomer(string $firstName, string $lastName): Customer
    {
        $customer = new Customer($this->generateCustomerNumber(), $firstName, $lastName);
        $this->customers[$customer->getCustomerNumber()] = $customer;
        echo "You have created a new customer."
            . "Please remember the customer number: " . $customer->getCustomerNumber() ."\n";
        return $customer;
    }

    /**
     * @param int $customerNumber
     * @param string $type
     * @param float $balance
     *
     * @return BankAccount
     * @throws InvalidAccountTypeException
     */
    public function createNewAccount(int $customerNumber, string $type, float $balance = 0.0): BankAccount
    {
        $customer = $this->customers[$customerNumber];
            switch($type) {
                case "S":
                case "s":
                    $bankAccount = new SavingsAccount($this->generateAccountNumber(), $customer, $balance);
                    $this->savingsAccounts[$bankAccount->getAccountNumber()] = $bankAccount;
                    break;
                case "C":
                case "c":
                    $bankAccount = new CheckingAccount($this->generateAccountNumber(), $customer, $balance);
                    $this->checkingAccounts[$bankAccount->getAccountNumber()] = $bankAccount;
                    break;
                default:
                    throw new InvalidAccountTypeException();
            }
        echo "You have created a new bank account. "
            . "Please remember the account number: " . $bankAccount->getAccountNumber() ."\n";
        return $bankAccount;
    }

    /**
     * @param string $accountNumber
     * @param float $amount
     * @return bool
     * @throws InvalidAmountException
     */
    public function doDeposit(string $accountNumber, float $amount) : bool{
        if (key_exists($accountNumber, $this->checkingAccounts)){
            $bankAccount = $this->checkingAccounts[$accountNumber];
            return $bankAccount->deposit($amount);
        }

        if (key_exists($accountNumber, $this->savingsAccounts)) {
            $bankAccount = $this->savingsAccounts[$accountNumber];
            return $bankAccount->deposit($amount);
        }
        return false;
    }

    /**
     * @param string $accountNumber
     * @param float $amount
     * @return bool
     * @throws InvalidAmountException
     */
    public function doDebit(string $accountNumber, float $amount) : bool{
        if (key_exists($accountNumber, $this->checkingAccounts)){
            $bankAccount = $this->checkingAccounts[$accountNumber];
            return $bankAccount->debit($amount);
        }

        if (key_exists($accountNumber, $this->savingsAccounts)) {
            $bankAccount = $this->savingsAccounts[$accountNumber];
            return $bankAccount->debit($amount);
        }

        return false;
    }

    /**
     * @param string $accountNumber
     * @return float
     */
    public function getBalance(string $accountNumber): float
    {
        if (key_exists($accountNumber, $this->checkingAccounts)){
            $bankAccount = $this->checkingAccounts[$accountNumber];
            return $bankAccount->getBalance();
        }

        if (key_exists($accountNumber, $this->savingsAccounts)) {
            $bankAccount = $this->savingsAccounts[$accountNumber];
            return $bankAccount->getBalance();
        }
        return 0.0;
    }

    /**
     * @param string $accountNumber
     * @return float
     * @throws InvalidAmountException
     */
    public function addInterest(string $accountNumber): float
    {
        if (key_exists($accountNumber, $this->savingsAccounts)) {
            $bankAccount = $this->savingsAccounts[$accountNumber];
            $bankAccount->addInterest();
            return $bankAccount->getBalance();
        }
        if (key_exists($accountNumber, $this->checkingAccounts)){
            $bankAccount = $this->checkingAccounts[$accountNumber];
            $bankAccount->addInterest();
            return $bankAccount->getBalance();
        }
        return 0.0;
    }

    /**
     * @throws NoValidAccountException
     */
    public function deductMaintenanceCharge(string $accountNumber): float
    {
        if(key_exists($accountNumber, $this->checkingAccounts)){
            $bankAccount = $this->checkingAccounts[$accountNumber];
            try {
                $bankAccount->deductAccountMaintenanceCharge();
            } catch (InvalidAmountException $e){
                echo $e->getDebitErrorMessage($bankAccount->getAccountMaintenanceCharge());
            }
        } else {
            throw new NoValidAccountException();
        }
        return $bankAccount->getBalance();
    }

    /**
     * return string
     */
    public function generateAccountNumber() : string{
        $this->accountNumberGen = ++$this->accountNumberGen;
        return (string)$this->accountNumberGen;
    }

    /**
     * return string
     */
    public function generateCustomerNumber() : int{
        $this->customerNumberGen = ++$this->customerNumberGen;
        return $this->customerNumberGen;
    }

    /**
     * @param Customer $customer
     * @return Customer $customer
     */
    public function addCustomer(Customer $customer) : Customer{
        $this->customers[$customer->getCustomerNumber()] = $customer;
        return $customer;
    }

    /**
     * @param int $customerNumber
     */
    public function deleteCustomer(int $customerNumber) : void{
        foreach ($this->checkingAccounts as $checkingAccount){
            if ($checkingAccount->getCustomer()->getCustomerNumber() == $customerNumber){
                unset($this->checkingAccounts[$checkingAccount->getAccountNumber()]);
            }
        }
        foreach ($this->savingsAccounts as $savingsAccount){
            if ($savingsAccount->getCustomer()->getCustomerNumber() == $customerNumber){
                unset($this->savingsAccounts[$savingsAccount->getAccountNumber()]);
            }
        }
        unset($this->customers[$customerNumber]);
    }

    /**
     * @param CheckingAccount|SavingsAccount $bankAccount
     * @return BankAccount
     */
    public function addBankAccount(SavingsAccount|CheckingAccount $bankAccount) : BankAccount {
        switch (get_class($bankAccount)){
            case SavingsAccount::class:
                $this->savingsAccounts[$bankAccount->getAccountNumber()] = $bankAccount;
                break;
            case CheckingAccount::class:
                $this->checkingAccounts[$bankAccount->getAccountNumber()] = $bankAccount;
                break;
        }
        return $bankAccount;
    }

    /**
     * @param string $accountNumber
     */
    public function deleteAccount(string $accountNumber) : void{
        if(key_exists($accountNumber, $this->checkingAccounts)) {
            unset($this->checkingAccounts[$accountNumber]);
        }

        if (key_exists($accountNumber, $this->savingsAccounts)){
            unset($this->savingsAccounts[$accountNumber]);
        }
    }

    /**
     * @throws NoCustomerException
     */
    public function checkCustomerNumber(int $customerNumber): bool
    {
        if(key_exists($customerNumber, $this->customers)){
            return true;
        } else {
            throw new NoCustomerException();
        }
    }

    /**
     * @throws NoAccountException
     */
    public function checkAccountNumber(string $accountNumber): bool
    {
        if(key_exists($accountNumber, $this->checkingAccounts) || key_exists($accountNumber, $this->savingsAccounts)){
            return true;
        } else {
            throw new NoAccountException();
        }
    }

    /**
     * @return Customer[]
     */
    public function getCustomers(): array
    {
        return $this->customers;
    }

    /**
     * @return SavingsAccount[]
     */
    public function getSavingsAccounts(): array
    {
        return $this->savingsAccounts;
    }

    /**
     * @return CheckingAccount[]
     */
    public function getCheckingAccounts(): array
    {
        return $this->checkingAccounts;
    }

}