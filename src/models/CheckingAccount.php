<?php

namespace ra\kp\models;

class CheckingAccount extends BankAccount
{
    private float $interestRate;

    /**
     * @param float $interestRate
     */
    public function __construct(string $accountNumber, Customer $customer, float $balance, float $interestRate = 0.05)
    {
        parent::__construct($accountNumber, $customer, $balance);
        $this->interestRate = $interestRate;
    }

    public function addInterest(){
        $interest = $this->getBalance() * $this->interestRate / 100;
        $this->deposit($interest);
    }

    /**
     * @return float
     */
    public function getInterestRate(): float
    {
        return $this->interestRate;
    }

    /**
     * @param float $interestRate
     */
    public function setInterestRate(float $interestRate): void
    {
        $this->interestRate = $interestRate;
    }
}