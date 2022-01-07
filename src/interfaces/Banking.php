<?php


namespace ra\kp\interfaces;


use ra\kp\models\Bank;
use ra\kp\models\BankAccount;
use ra\kp\models\CheckingAccount;
use ra\kp\models\Customer;
use ra\kp\models\SavingsAccount;

interface Banking
{
    /**
     * @param BankAccount $from
     * @param BankAccount $to
     * @param float $amount
     * @return bool
     */
    function doTransaction(BankAccount $from, BankAccount $to, float $amount) : bool;

    /**
     * @param string $from
     * @param string $to
     * @param float $amount
     * @return bool
     */
    function doTransactionWithAccNumber(string $from, string $to, float $amount) : bool;

    /**
     * @param int $customerNumber
     * @param string $type
     * @param float $balance
     * @return BankAccount
     */
    function createNewAccount(int $customerNumber, string $type, float $balance = 0.0): BankAccount;

    /**
     * @param string $firstName
     * @param string $lastName
     * @param string $address
     * @param string $birthday
     * @return Customer|null
     */
    function createNewCustomer(string $firstName, string $lastName, string $address, string $birthday) : ?Customer;

    /**
     * @param string $accountNumber
     * @param float $amount
     * @return bool
     */
    function doDeposit(string $accountNumber, float $amount) : bool;

    /**
     * @param string $accountNumber
     * @param float $amount
     * @return bool
     */
    function doDebit(string $accountNumber, float $amount) : bool;

    /**
     * @param string $accountNumber
     * @return float
     */
    function getBalance(string $accountNumber) : float;
}