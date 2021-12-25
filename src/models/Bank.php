<?php
namespace ra\kp\models;

use Exception;
use DateTime;
use ra\kp\models\BankAccount;
use ra\kp\models\Customer;
use ra\kp\exceptions\NoAccountException;
use ra\kp\exceptions\NoCustomerException;
use ra\kp\exceptions\NoValidAccountException;
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

    /** @var BankAccount[] */
    private array $bankAccounts;

    /** @var SavingsAccount[] */
    private array $savingsAccounts;

    /** @var CheckingAccount[] */
    private array $checkingAccounts;

    public function __construct()
    {
        $this->accountNumberGen = 1000;
        $this->customerNumberGen = 0;
        $this->customers = [];
        $this->bankAccounts = [];
        $this->savingsAccounts = [];
        $this->checkingAccounts = [];
    }


    /**
     * @param BankAccount $from
     * @param BankAccount $to
     * @param float $amount
     * @return bool
     */
    public function doTransaction(BankAccount $from, BankAccount $to, float $amount) : bool{
        try {
            if ($amount > 0) {
                $debit = $from->debit($amount);
                if ($debit) {
                    return $to->deposit($amount);
                }
            } else {
                throw new TransactionFailedException();
            }
        } catch (TransactionFailedException $e){
            echo $e->getErrorMessage();
        }
        return false;
    }

    /**
     * @param string $from
     * @param string $to
     * @param float $amount
     * @return bool
     * @throws Exception
     */
    public function doTransactionWithAccNumber(string $from, string $to, float $amount) : bool{
        try{
            if(key_exists($from, $this->bankAccounts) && key_exists($to, $this->bankAccounts)){
                $fromAccount = $this->bankAccounts[$from];
                $toAccount = $this->bankAccounts[$to];
                return $this->doTransaction($fromAccount, $toAccount, $amount);
            } else {
                throw new TransactionFailedException("The account numbers are invalid! ");
            }
        } catch (TransactionFailedException $e){
            echo $e->getMessage() . $e->getErrorMessage();
        }
        return false;
    }

    /**
     * @throws Exception
     */
    function createNewCustomer(string $firstName, string $lastName, string $address, string $birthday): ?Customer
    {
        try{
            $birthday = new DateTime($birthday);
            $customer = new Customer($this->generateCustomerNumber(), $firstName, $lastName, $address, $birthday);
            $this->customers[$customer->getCustomerNumber()] = $customer;
            echo "You have created a new customer."
                . "Please remember the customer number: " . $customer->getCustomerNumber();
            return $customer;
        } catch (Exception $e){
            echo $e->getMessage();
        }
        return null;
    }

    /**
     * @param int $customerNumber
     * @param string $type
     * @param float $balance
     *
     * @return null|BankAccount
     */
    public function createNewAccount(int $customerNumber, string $type, float $balance = 0.0): ?BankAccount
    {
        try{
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
                            $bankAccount = new BankAccount($this->generateAccountNumber(), $customer, $balance);
                            break;
                    }
                $this->bankAccounts[$bankAccount->getAccountNumber()] = $bankAccount;
                echo "You have created a new bank account. "
                    . "Please remember the account number: " . $bankAccount->getAccountNumber();
                return $bankAccount;
            } else {
                throw new NoCustomerException();
            }
        } catch (NoCustomerException $e){
            echo $e->getErrorMessage();
        }
        return null;
    }

    /**
     * @param string $accountNumber
     * @param float $amount
     * @return bool
     */
    public function doDeposit(string $accountNumber, float $amount) : bool{
        try{
            if (key_exists($accountNumber, $this->bankAccounts)) {
                $bankAccount = $this->bankAccounts[$accountNumber];
                return $bankAccount->deposit($amount);
            } else {
                throw new NoAccountException();
            }
        } catch (NoAccountException $e) {
            echo $e->getErrorMessage();
        }

        return false;
    }

    /**
     * @param string $accountNumber
     * @param float $amount
     * @return bool
     */
    public function doDebit(string $accountNumber, float $amount) : bool{
        try{
            if (key_exists($accountNumber, $this->bankAccounts)) {
                $bankAccount = $this->bankAccounts[$accountNumber];
                $bankAccount->debit($amount);
            } else {
                throw new NoAccountException();
            }
        } catch (NoAccountException $e) {
            echo $e->getErrorMessage();
        }
        return false;
    }

    /**
     * @param string $accountNumber
     * @return float
     */
    public function getBalance(string $accountNumber): float
    {
        try{
            if (key_exists($accountNumber, $this->bankAccounts)) {
                $bankAccount = $this->bankAccounts[$accountNumber];
                return $bankAccount->getBalance();
            } else {
                throw new NoAccountException();
            }
        } catch (NoAccountException $e) {
            echo $e->getErrorMessage();
        }

        return 0.0;
    }

    /**
     * @param string $accountNumber
     * @return float
     */
    public function addInterest(string $accountNumber): float
    {
        try{
            if (key_exists($accountNumber, $this->savingsAccounts)) {
                $bankAccount = $this->savingsAccounts[$accountNumber];
                $bankAccount->addInterest();
            } else if (key_exists($accountNumber, $this->checkingAccounts)){
                $bankAccount = $this->savingsAccounts[$accountNumber];
                $bankAccount->addInterest();
            } else if (key_exists($accountNumber, $this->bankAccounts)){
                throw new NoValidAccountException();
            } else {
                throw new NoAccountException();
            }
            return $bankAccount->getBalance();
        } catch (NoValidAccountException $e) {
            echo $e->getErrorMessage();
        } catch (NoAccountException $e){
            echo $e->getErrorMessage();
        }
        return 0.0;
    }

    /**
     * return string
     */
    private function generateAccountNumber() : string{
        $this->accountNumberGen = ++$this->accountNumberGen;
        return (string)$this->accountNumberGen;
    }

    /**
     * return string
     */
    private function generateCustomerNumber() : int{
        $this->customerNumberGen = ++$this->customerNumberGen;
        return $this->customerNumberGen;
    }

    /**
     * @return Customer[]
     */
    public function getCustomers(): array
    {
        return $this->customers;
    }

    /**
     * @return BankAccount[]
     */
    public function getBankAccounts(): array
    {
        return $this->bankAccounts;
    }
}