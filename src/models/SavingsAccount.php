<?php
namespace ra\kp\models;

use ra\kp\exceptions\InvalidAmountException;

class SavingsAccount extends BankAccount
{
    /** @var float */
    private float $interestRate;

    /**
     * @param string $accountNumber
     * @param Customer $customer
     * @param float $balance
     * @param float $interestRate
     */
    public function __construct(string $accountNumber, Customer $customer, float $balance, float $interestRate = 0.8)
    {
        parent::__construct($accountNumber, $customer, $balance);
        $this->interestRate = $interestRate;
    }

    /**
     * @return string
     */
    public function showInfos(): string
    {
        return "\t".$this->getAccountNumber()."\t|\tSavings account\t\t|\t".
            $this->getCustomer()->getCustomerNumber()."\t|\t" .
            $this->getBalance()."\t\t|\t\t".
            $this->getInterestRate()."\n";
    }

    /**
     * @throws InvalidAmountException
     */
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