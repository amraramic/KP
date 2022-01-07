<?php
namespace ra\kp\models;

use Exception;
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
            throw new TransactionFailedException("Make sure that the amount is greater than 0 and that there is enough money to be debited!");
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
            throw new TransactionFailedException("The account numbers are invalid! ");
        }
    }

    function createNewCustomer(string $firstName, string $lastName): ?Customer
    {
        $customer = new Customer($this->generateCustomerNumber(), $firstName, $lastName);
        $this->customers[$customer->getCustomerNumber()] = $customer;
        echo "You have created a new customer."
            . "Please remember the customer number: " . $customer->getCustomerNumber();
        return $customer;
    }

    /**
     * @param int $customerNumber
     * @param string $type
     * @param float $balance
     *
     * @return BankAccount
     * @throws InvalidAccountTypeException
     * @throws NoCustomerException
     */
    public function createNewAccount(int $customerNumber, string $type, float $balance = 0.0): BankAccount
    {
        if(key_exists($customerNumber, $this->customers)){
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
                . "Please remember the account number: " . $bankAccount->getAccountNumber();
            return $bankAccount;
        } else {
            throw new NoCustomerException();
        }
    }

    /**
     * @param string $accountNumber
     * @param float $amount
     * @return bool
     * @throws NoAccountException|InvalidAmountException
     */
    public function doDeposit(string $accountNumber, float $amount) : bool{
        if (key_exists($accountNumber, $this->checkingAccounts)){
            $bankAccount = $this->checkingAccounts[$accountNumber];
            return $bankAccount->deposit($amount);
        } else if (key_exists($accountNumber, $this->savingsAccounts)) {
            $bankAccount = $this->savingsAccounts[$accountNumber];
            return $bankAccount->deposit($amount);
        } else {
            throw new NoAccountException();
        }
    }

    /**
     * @param string $accountNumber
     * @param float $amount
     * @return bool
     * @throws NoAccountException|InvalidAmountException
     */
    public function doDebit(string $accountNumber, float $amount) : bool{
        if (key_exists($accountNumber, $this->checkingAccounts)){
            $bankAccount = $this->checkingAccounts[$accountNumber];
            return $bankAccount->debit($amount);
        } else if (key_exists($accountNumber, $this->savingsAccounts)) {
            $bankAccount = $this->savingsAccounts[$accountNumber];
            return $bankAccount->debit($amount);
        } else {
            throw new NoAccountException();
        }
    }

    /**
     * @param string $accountNumber
     * @return float
     * @throws NoAccountException
     */
    public function getBalance(string $accountNumber): float
    {
        if (key_exists($accountNumber, $this->checkingAccounts)){
            $bankAccount = $this->checkingAccounts[$accountNumber];
            return $bankAccount->getBalance();
        } else if (key_exists($accountNumber, $this->savingsAccounts)) {
            $bankAccount = $this->savingsAccounts[$accountNumber];
            return $bankAccount->getBalance();
        } else {
            throw new NoAccountException();
        }
    }

    /**
     * @param string $accountNumber
     * @return float
     * @throws NoAccountException
     * @throws InvalidAmountException
     */
    public function addInterest(string $accountNumber): float
    {
        if (key_exists($accountNumber, $this->savingsAccounts)) {
            $bankAccount = $this->savingsAccounts[$accountNumber];
            $bankAccount->addInterest();
        } else if (key_exists($accountNumber, $this->checkingAccounts)){
            $bankAccount = $this->checkingAccounts[$accountNumber];
            $bankAccount->addInterest();
        } else {
            throw new NoAccountException();
        }
        return $bankAccount->getBalance();
    }

    /**
     * @throws NoValidAccountException
     * @throws NoAccountException
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
        } else if (key_exists($accountNumber, $this->savingsAccounts)){
            throw new NoValidAccountException();
        } else {
            throw new NoAccountException();
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
     * @throws NoCustomerException
     */
    public function deleteCustomer(int $customerNumber) : void{
        if(key_exists($customerNumber, $this->customers)) {
            unset($this->customers[$customerNumber]);
        } else {
            throw new NoCustomerException();
        }
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
     * @throws NoAccountException
     */
    public function deleteAccount(string $accountNumber) : void{
        if(key_exists($accountNumber, $this->checkingAccounts)) {
            unset($this->checkingAccounts[$accountNumber]);
        } elseif (key_exists($accountNumber, $this->savingsAccounts)){
            unset($this->savingsAccounts[$accountNumber]);
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