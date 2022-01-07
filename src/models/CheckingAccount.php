<?php

namespace ra\kp\models;

use ra\kp\exceptions\InvalidAmountException;

class CheckingAccount extends BankAccount
{
    private float $interestRate;

    private float $accountMaintenanceCharge;
    /**
     * @param float $interestRate
     */
    public function __construct(string $accountNumber, Customer $customer, float $balance, float $interestRate = 0.05, float $accountMaintenanceCharge = 7.0)
    {
        parent::__construct($accountNumber, $customer, $balance);
        $this->interestRate = $interestRate;
        $this->accountMaintenanceCharge = $accountMaintenanceCharge;
    }

    /**
     * @throws InvalidAmountException
     */
    public function addInterest(){
        $interest = $this->getBalance() * $this->interestRate / 100;
        $this->deposit($interest);
    }

    /**
     * @throws InvalidAmountException
     */
    public function deductAccountMaintenanceCharge(): bool
    {
        return $this->debit($this->accountMaintenanceCharge);
    }

    /**
     * @param float $interestRate
     */
    public function setInterestRate(float $interestRate): void
    {
        $this->interestRate = $interestRate;
    }
}