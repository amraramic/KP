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
        $customer1 = new Customer($this->bank->generateCustomerNumber(), "Max", "Mustemann");
        $customer2 = new Customer($this->bank->generateCustomerNumber(),"Marie", "Meier");
        $this->bank->addCustomer($customer1);
        $this->bank->addCustomer($customer2);
        $account1 = new CheckingAccount($this->bank->generateAccountNumber(), $customer1, 100);
        $account2 = new SavingsAccount($this->bank->generateAccountNumber(), $customer2, 50, 0.03);
        $this->bank->addBankAccount($account1);
        $this->bank->addBankAccount($account2);


        $this->startMenu();
    }

    function startMenu(){
        echo  "\033[92m\n\nCreate customer = CUST, Create account = ACC, Do transaction = TRANS, Deposit = DEP, Debit = DEB,\n" .
            "See balance = BAL, Add interest = INTER, Deduct Account Maintenance Charge = MAINT, Overview = ALL\n".
            "Delete customer = DELCUST, Delete account = DELACC, Back to start menu = EXIT\n\n\033[0m\n";
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
            case "DELCUST":
            case "delcust":
                $this->deleteCustomer();
                $this->startMenu();
                break;
            case "DELACC":
            case "delacc":
                $this->deleteAccount();
                $this->startMenu();
                break;
            case "EXIT":
            case "exit":
                $this->startMenu();
                break;
            default:
                echo "This command does not exist. Please try again!";
                $this->startMenu();
                break;
        }
    }

    function createCustomer(){
        echo "Please enter your data:\n";
        $firstname = readline("Firstname: ");
        $this->checkExit($firstname);
        $lastname = readline("Lastname: ");
        $this->checkExit($lastname);
        try {
            $this->bank->createNewCustomer($firstname, $lastname);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    function createAccount(){
        echo "Please enter your data:\n";
        $customerNumber = readline("Customer number: ");
        $this->checkExit($customerNumber);
        try {
            $this->bank->checkCustomerNumber((int) $customerNumber);
        } catch (NoCustomerException $e){
            echo $e->getErrorMessage();
            $this->startMenu();
        }
        $type = readline("Typ of account: (S = Savings/C = Checking): ");
        $this->checkExit($type);
        try {
            $this->bank->createNewAccount((int) $customerNumber, $type);
        } catch (InvalidAccountTypeException $e){
            echo $e->getErrorMessage();
        }
    }

    function doTransaction(){
        echo "Please enter the data:\n";
        $from = readline("From: ");
        $this->checkExit($from);
        try {
            $this->bank->checkAccountNumber($from);
        } catch (NoAccountException $e){
            echo $e->getErrorMessage();
            $this->startMenu();
        }
        $to = readline("To: ");
        $this->checkExit($to);
        try {
            $this->bank->checkAccountNumber($to);
        } catch (NoAccountException $e){
            echo $e->getErrorMessage();
            $this->startMenu();
        }
        $amount = readline("Amount: ");
        $this->checkExit($amount);

        try {
            $this->bank->doTransactionWithAccNumber($from, $to, (float) $amount);
        } catch (TransactionFailedException $e) {
            echo $e->getErrorMessage();
            echo $e->getMessage();
        } catch (InvalidAmountException $e) {
        }
    }

    function deposit(){
        echo "Please enter the data:\n";
        $account = readline("Account number: ");
        $this->checkExit($account);
        try {
            $this->bank->checkAccountNumber($account);
        } catch (NoAccountException $e){
            echo $e->getErrorMessage();
            $this->startMenu();
        }
        $amount = readline("Amount: ");
        $this->checkExit($amount);
        try{
            $this->bank->doDeposit($account, (float) $amount);
        } catch (InvalidAmountException $e){
            echo $e->getDepositErrorMessage((float) $amount);
        }
    }

    function debit(){
        echo "Please enter the data:\n";
        $account = readline("Account number: ");
        $this->checkExit($account);
        try {
            $this->bank->checkAccountNumber($account);
        } catch (NoAccountException $e){
            echo $e->getErrorMessage();
            $this->startMenu();
        }
        $amount = readline("Amount: ");
        $this->checkExit($amount);
        try{
            $this->bank->doDebit($account, (float) $amount);
        } catch (InvalidAmountException $e){
            echo $e->getDebitErrorMessage((float) $amount);
        }
    }

    function showBalance(){
        echo "Please enter the data:\n";
        $account = readline("Account number: ");
        $this->checkExit($account);
        try {
            $this->bank->checkAccountNumber($account);
        } catch (NoAccountException $e){
            echo $e->getErrorMessage();
            $this->startMenu();
        }

        $balance = $this->bank->getBalance($account);
        echo "Balance of " . $account .": " . $balance ." $\n";
    }

    function addInterest(){
        echo "Please enter the data:\n";
        $account = readline("Account number: ");
        $this->checkExit($account);
        try {
            $this->bank->checkAccountNumber($account);
        } catch (NoAccountException $e){
            echo $e->getErrorMessage();
            $this->startMenu();
        }
        try {
            echo "Balance after adding interest: " . $this->bank->addInterest($account) ."\n";
        } catch (InvalidAmountException $e) {
            echo $e->getMessage();
        }
    }

    function deductMaintenanceCharge() {
        $account = readline("Account number: ");
        $this->checkExit($account);
        try {
            $this->bank->checkAccountNumber($account);
        } catch (NoAccountException $e){
            echo $e->getErrorMessage();
            $this->startMenu();
        }
        try {
            echo "Balance after deducting account maintenance charge: " . $this->bank->deductMaintenanceCharge($account) ."\n";
        } catch (NoValidAccountException $e){
            echo $e->getCheckingAccountErrorMessage();
        }
    }

    function deleteCustomer(){
        echo "Attention: If you delete this customer, all his/her accounts will be deleted!\n";
        $customer = readline("Customer number: ");
        $this->checkExit($customer);
        try {
            $this->bank->checkCustomerNumber((int)$customer);
        } catch (NoCustomerException $e){
            echo $e->getErrorMessage();
            $this->startMenu();
        }
        $this->bank->deleteCustomer((int) $customer);
    }

    function deleteAccount(){
        $account = readline("Account number: ");
        $this->checkExit($account);
        try {
            $this->bank->checkAccountNumber($account);
        } catch (NoAccountException $e){
            echo $e->getErrorMessage();
            $this->startMenu();
        }
        $this->bank->deleteAccount($account);
    }

    function showAll(){
        $customers = $this->bank->getCustomers();
        print_r("\tCustNr\t|\tName\n");
        foreach ($customers as $customer) {
            print_r("\t".$customer->getCustomerNumber()."\t|\t".$customer->getFirstName()." ".$customer->getLastName()."\n");
        }
        $savingsAccounts = $this->bank->getSavingsAccounts();
        $checkingAccounts = $this->bank->getCheckingAccounts();
        print_r("\n\tAccNr\t|\t\tAccType\t\t|\tCustNr\t|\tBalance\t\t|\tInteresst rate\t\t|\tAccount maintenance charge\n");
        foreach ($savingsAccounts as $account){
            print_r($account->showInfos());
        }
        foreach ($checkingAccounts as $account){
            print_r($account->showInfos());
        }
    }

    function checkExit($input){
        if($input == "exit" || $input == "EXIT") {
            $this->startMenu();
        }
    }
}