<?php
namespace ra\kp;

use Exception;
use ra\kp\exceptions\InvalidAccountTypeException;
use ra\kp\exceptions\InvalidAmountException;
use ra\kp\exceptions\NoAccountException;
use ra\kp\exceptions\NoCustomerException;
use ra\kp\exceptions\NoValidAccountException;
use ra\kp\exceptions\TransactionFailedException;
use ra\kp\models\Bank;
use ra\kp\models\BankAccount;

class BankDemo
{
    private Bank $bank;

    /**
     * @throws Exception
     */
    public function startDemo()
    {
        $this->bank = new Bank();
        $customer = $this->bank->createNewCustomer("Amra", "Ramic", "Bla", "1.1.2000");
        $this->bank->createNewAccount($customer->getCustomerNumber(), "s", 100);
        $customer2 = $this->bank->createNewCustomer("Emre", "Ekici", "Bla", "1.1.2000");
        $this->bank->createNewAccount($customer2->getCustomerNumber(), "c", 50);
        $this->startMenu();
    }

    /**
     * @throws Exception
     */
    function startMenu(){
        echo("\nCreate customer = CUST, Create account = ACC, Do transaction = TRANS, Deposit = DEP, Debit = DEB, See balance = BAL, Add interest = INTER, Deduct Account Maintenance Charge = MAINT, Overview = ALL\n"
            . "What do you want to do? Type a key word: ");
        $command = readline("What do you want wo do? Type key word: ");

        switch ($command){
            case "CUST":
            case "cust":
                $this->createCustomer();
                $this->startMenu();
                break;
            case "ACC":
            case "acc":
                $this->createAccount();
                $this->startMenu();
                break;
            case "TRANS":
            case "trans":
                $this->doTransaction();
                $this->startMenu();
                break;
            case "DEB":
            case "deb":
                $this->debit();
                $this->startMenu();
                break;
            case "DEP":
            case "dep":
                $this->deposit();
                $this->startMenu();
                break;
            case "BAL":
            case "bal":
                $this->showBalance();
                $this->startMenu();
                break;
            case "INTER":
            case "inter":
                $this->addInterest();
                $this->startMenu();
                break;
            case "maint":
            case "MAINT":
                $this->deductMaintenanceCharge();
                $this->startMenu();
                break;
            case "ALL":
            case "all":
                $this->showAll();
                $this->startMenu();
                break;
            default:
                echo "This command does not exist. Please try again!";
                $this->startMenu();
                break;
        }
    }

    /**
     * @throws Exception
     */
    function createCustomer(){
        echo "Please enter your data:\n";
        echo "Firstname: ";
        $firstname = readline();
        echo "Lastname: ";
        $lastname = readline();
        echo "Address: ";
        $address = readline();
        echo "Birthday ";
        $birthday = readline();
        try {
            $this->bank->createNewCustomer($firstname, $lastname, $address, $birthday);
        } catch (Exception $e){
            echo $e->getMessage();
        }
    }

    function createAccount(){
        echo "Please enter your data:\n";
        echo "Customer number: ";
        $customerNumber = (int) readline();
        echo "Typ of account: (S = Savings/C = Checking): ";
        $type = readline();
        try {
            $this->bank->createNewAccount($customerNumber, $type);
        } catch (NoCustomerException $e){
            echo $e->getErrorMessage();
        } catch (InvalidAccountTypeException $e){
            echo $e->getErrorMessage();
        }
    }

    function doTransaction(){
        echo "Please enter the data:\n";
        echo "From: ";
        $from = readline();
        echo "To: ";
        $to = readline();
        echo "Amount: ";
        $amount = (float) readline();

        try {
            $this->bank->doTransactionWithAccNumber($from, $to, $amount);
        } catch (TransactionFailedException $e) {
            echo $e->getErrorMessage();
        }
    }

    function deposit(){
        echo "Please enter the data:\n";
        echo "Account number: ";
        $account = readline();
        echo "Amount: ";
        $amount = (float) readline();
        try{
            $this->bank->doDeposit($account, $amount);
        } catch (InvalidAmountException $e){
            echo $e->getDepositErrorMessage($amount);
        } catch (NoAccountException $e){
            echo $e->getErrorMessage();
        }
    }

    function debit(){
        echo "Please enter the data:\n";
        echo "Account number: ";
        $account = readline();
        echo "Amount: ";
        $amount = (float) readline();
        try{
            $this->bank->doDebit($account, $amount);
        } catch (InvalidAmountException $e){
            echo $e->getDebitErrorMessage($amount);
        } catch (NoAccountException $e){
            echo $e->getErrorMessage();
        }
    }

    function showBalance(){
        echo "Please enter the data:\n";
        echo "Account number: ";
        $account = readline();
        try {
            $balance = $this->bank->getBalance($account);
            echo "Balance of " . $account .": " . $balance ."\n";
        } catch (NoAccountException $e) {
            echo $e->getErrorMessage();
        }
    }

    function addInterest(){
        echo "Please enter the data:\n";
        echo "Account number: ";
        $account = readline();
        try {
            echo "Balance after adding interest: " . $this->bank->addInterest($account) ."\n";
        } catch (NoValidAccountException $e){
            echo $e->getErrorMessage();
        } catch (NoAccountException $e){
            echo $e->getErrorMessage();
        }
    }

    function deductMaintenanceCharge() {
        echo "Account number: ";
        $account = readline();
        try {
            echo "Balance after deducting account maintenance charge: " . $this->bank->deductMaintenanceCharge($account) ."\n";
        } catch (NoValidAccountException $e){
            echo $e->getErrorMessage();
        } catch (NoAccountException $e){
            echo $e->getErrorMessage();
        }
    }

    function showAll(){
        $bankAccounts = $this->bank->getBankAccounts();
        print_r("AccNr | CustNr | Firstname | Lastname | Balance\n");
        foreach ($bankAccounts as $account){
            print_r($account->getAccountNumber()."   |   ".$account->getCustomer()->getCustomerNumber()."    |    "
                .$account->getCustomer()->getFirstName()."   |   ".$account->getCustomer()->getLastName()."  |  ".
                $account->getBalance()."\n");
        }
    }
}