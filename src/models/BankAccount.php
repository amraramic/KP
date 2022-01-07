<?php

namespace ra\kp\models;

use ra\kp\exceptions\InvalidAmountException;

class BankAccount
{
    /** @var string */
    private string $accountNumber;

    /** @var Customer */
    private Customer $customer;

    /** @var float */
    private float $balance;

    /**
     * @param string $accountNumber
     * @param Customer $customer
     * @param float $balance
     */
    public function __construct(string $accountNumber, Customer $customer, float $balance = 0.0)
    {
        $this->accountNumber = $accountNumber;
        $this->customer = $customer;
        $this->balance = $balance;
    }


    /**
     * @param float $amount
     * @return bool
     * @throws InvalidAmountException
     */
    public function debit(float $amount) : bool
    {
        if($amount < $this->balance){
            $this->balance = $this->balance - $amount;
            printf("You have successfully debit %s $ to %s account!\nNew balance: %s\n",
                $amount, $this->getAccountNumber(), $this->balance);
            return true;
        } else {
            throw new InvalidAmountException();
        }
    }

    /**
     * @param float $amount
     * @return bool
     * @throws InvalidAmountException
     */
    public function deposit(float $amount) : bool
    {
        if($amount > 0) {
            $this->balance = $this->balance + $amount;
            printf("You have successfully deposit %s $ to %s account!\nNew balance: %s\n",
                $amount, $this->getAccountNumber(), $this->balance);
            return true;
        } else {
            throw new InvalidAmountException();
        }
    }

    /**
     * @return string
     */
    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }

    /**
     * @param string $accountNumber
     */
    public function setAccountNumber(string $accountNumber): void
    {
        $this->accountNumber = $accountNumber;
    }

    /**
     * @return Customer
     */
    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    /**
     * @param Customer $customer
     */
    public function setCustomer(Customer $customer): void
    {
        $this->customer = $customer;
    }

    /**
     * @return float
     */
    public function getBalance(): float
    {
        return $this->balance;
    }

    /**
     * @param float $balance
     */
    public function setBalance(float $balance): void
    {
        $this->balance = $balance;
    }
}