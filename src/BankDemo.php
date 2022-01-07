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
use ra\kp\models\CheckingAccount;
use ra\kp\models\Customer;
use ra\kp\models\SavingsAccount;

class BankDemo
{
    private Bank $bank;

    /**
     * @throws Exception
     */
    public function startDemo()
    {
        $this->bank = new Bank();
        $customer1 = new Customer($this->bank->generateCustomerNumber(), "Amra", "Ramic", "Bla", new \DateTime("1.1.2000"));
        $customer2 = new Customer($this->bank->generateCustomerNumber(),"Emre", "Ekici", "Bla", new \DateTime("1.1.2000"));
        $this->bank->addCustomer($customer1);
        $this->bank->addCustomer($customer2);
        $account1 = new CheckingAccount($this->bank->generateAccountNumber(), $customer1, 100);
        $account2 = new SavingsAccount($this->bank->generateAccountNumber(), $customer2, 50, 0.03);
        $this->bank->addBankAccount($account1);
        $this->bank->addBankAccount($account2);


        $this->startMenu();
    }

    /**
     * @throws Exception
     */
    function startMenu(){
        echo("\nCreate customer = CUST, Create account = ACC, Do transaction = TRANS, Deposit = DEP, Debit = DEB,\n" .
            "See balance = BAL, Add interest = INTER, Deduct Account Maintenance Charge = MAINT, Overview = ALL\n\n"
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
        echo "Birthday: ";
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
            echo $e->getMessage();
        } catch (InvalidAmountException $e) {
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
            echo "Balance of " . $account .": " . $balance ." $\n";
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
            echo $e->getCheckingAccountErrorMessage();
        } catch (NoAccountException $e){
            echo $e->getErrorMessage();
        }
    }

    function showAll(){
        $savingsAccounts = $this->bank->getSavingsAccounts();
        $checkingAccounts = $this->bank->getCheckingAccounts();
        print_r("AccNr |      AccType       | CustNr | Firstname | Lastname | Balance\n");
        foreach ($savingsAccounts as $account){
            print_r($account->getAccountNumber()."   |   SavingsAccount   |   ".$account->getCustomer()->getCustomerNumber()."    |    "
                .$account->getCustomer()->getFirstName()."   |   ".$account->getCustomer()->getLastName()."  |  ".
                $account->getBalance()."\n");
        }
        foreach ($checkingAccounts as $account){
            print_r($account->getAccountNumber()."   |   CheckingAccount  |   ".$account->getCustomer()->getCustomerNumber()."    |    "
                .$account->getCustomer()->getFirstName()."   |   ".$account->getCustomer()->getLastName()."  |  ".
                $account->getBalance()."\n");
        }
    }
}