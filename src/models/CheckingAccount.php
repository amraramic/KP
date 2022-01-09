<?php

namespace ra\kp\models;

use ra\kp\exceptions\InvalidAmountException;

class CheckingAccount extends BankAccount
{
    private float $interestRate;

    private float $accountMaintenanceCharge;

    /**
     * @param string $accountNumber
     * @param Customer $customer
     * @param float $balance
     * @param float $interestRate
     * @param float $accountMaintenanceCharge
     */
    public function __construct(string $accountNumber, Customer $customer, float $balance, float $interestRate = 0.05, float $accountMaintenanceCharge = 7.0)
    {
        parent::__construct($accountNumber, $customer, $balance);
        $this->interestRate = $interestRate;
        $this->accountMaintenanceCharge = $accountMaintenanceCharge;
    }

    /**
     * @return string
     */
    public function showInfos(): string
    {
        return $this->getAccountNumber()."   |  Checking account  |   ".
            $this->getCustomer()->getCustomerNumber()."    |    " .
            $this->getCustomer()->getFirstName()."   |   ".
            $this->getCustomer()->getLastName()."  |  ".
            $this->getBalance()."    |     ".
            $this->getInterestRate()."        |     ".
            $this->getAccountMaintenanceCharge()."\n";
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
     * @return float
     */
    public function getInterestRate(): float
    {
        return $this->interestRate;
    }

    /**
     * @return float
     */
    public function getAccountMaintenanceCharge(): float
    {
        return $this->accountMaintenanceCharge;
    }

    /**
     * @param float $interestRate
     */
    public function setInterestRate(float $interestRate): void
    {
        $this->interestRate = $interestRate;
    }
}