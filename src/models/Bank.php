<?php
namespace ra\kp\models;

use Exception;
use DateTime;
use phpDocumentor\Reflection\Types\This;
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

    /** @var BankAccount[] */
    private array $bankAccounts;

    /** @var SavingsAccount[] */
    private array $savingsAccounts;

    /** @var CheckingAccount[] */
    private array $checkingAccounts;

    public function __construct()
    {
        $this->accountNumberGen = 100;
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
     * @throws TransactionFailedException|InvalidAmountException
     */
    public function doTransaction(BankAccount $from, BankAccount $to, float $amount) : bool{
        if ($amount > 0) {
            $debit = $from->debit($amount);
            if ($debit) {
                return $to->deposit($amount);
            }
        } else {
            throw new TransactionFailedException();
        }

        return false;
    }

    /**
     * @param string $from
     * @param string $to
     * @param float $amount
     * @return bool
     * @throws TransactionFailedException
     */
    public function doTransactionWithAccNumber(string $from, string $to, float $amount) : bool{
        if(key_exists($from, $this->bankAccounts) && key_exists($to, $this->bankAccounts)){
            $fromAccount = $this->bankAccounts[$from];
            $toAccount = $this->bankAccounts[$to];
            return $this->doTransaction($fromAccount, $toAccount, $amount);
        } else {
            throw new TransactionFailedException("The account numbers are invalid! ");
        }
    }

    /**
     * @throws Exception
     */
    function createNewCustomer(string $firstName, string $lastName, string $address, string $birthday): ?Customer
    {
        $birthday = new DateTime($birthday);
        $customer = new Customer($this->generateCustomerNumber(), $firstName, $lastName, $address, $birthday);
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
     * @return null|BankAccount
     * @throws NoCustomerException
     * @throws InvalidAccountTypeException
     */
    public function createNewAccount(int $customerNumber, string $type, float $balance = 0.0): ?BankAccount
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
            $this->bankAccounts[$bankAccount->getAccountNumber()] = $bankAccount;
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
        if (key_exists($accountNumber, $this->bankAccounts)) {
            $bankAccount = $this->bankAccounts[$accountNumber];
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
        if (key_exists($accountNumber, $this->bankAccounts)) {
            $bankAccount = $this->bankAccounts[$accountNumber];
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
        if (key_exists($accountNumber, $this->bankAccounts)) {
            $bankAccount = $this->bankAccounts[$accountNumber];
            return $bankAccount->getBalance();
        } else {
            throw new NoAccountException();
        }
    }

    /**
     * @param string $accountNumber
     * @return float
     */
    public function addInterest(string $accountNumber): float
    {
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
        } else if (key_exists($accountNumber, $this->bankAccounts) || key_exists($accountNumber, $this->savingsAccounts)){
            throw new NoValidAccountException();
        } else {
            throw new NoAccountException();
        }
        return $bankAccount->getBalance();
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
     * @param Customer $customer
     * @return Customer $customer
     */
    public function addCustomer(Customer $customer) : Customer{
        $this->customers[$customer->getCustomerNumber()] = $customer;
        return $customer;
    }

    /**
     * @param BankAccount $bankAccount
     * @return BankAccount $bankAccount
     */
    public function addBankAccount(BankAccount $bankAccount) : BankAccount {
        $this->bankAccounts[$bankAccount->getAccountNumber()] = $bankAccount;
        return $bankAccount;
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