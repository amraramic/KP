<?php
namespace ra\kp;

use Exception;
use ra\kp\models\Bank;

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
        $customer2 = $this->bank->createNewCustomer("EMre", "Ekici", "Bla", "1.1.2000");
        $this->bank->createNewAccount($customer2->getCustomerNumber(), "c", 50);
        //$this->bank->createNewAccount("Emre", "Ekici", "Bla", "1.1.2000", 100);
        $this->startMenu();
    }

    /**
     * @throws Exception
     */
    function startMenu(){
        echo("\nCreate customer = CUST, Create account = ACC, Do transaction = TRANS, Deposit = DEP, Debit = DEB, See balance = BAL, Add interest = INTER, Overview = ALL\n"
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
        $this->bank->createNewCustomer($firstname, $lastname, $address, $birthday);
    }

    /**
     * @throws Exception
     */
    function createAccount(){
        echo "Please enter your data:\n";
        echo "Typ of account: (S = Savings/C = Checking): ";
        $type = readline();
        echo "Customer number: ";
        $customerNumber = readline();
        $this->bank->createNewAccount($customerNumber, $type);
    }

    /**
     * @throws Exception
     */
    function doTransaction(){
        echo "Please enter the data:\n";
        echo "From: ";
        $from = readline();
        echo "To: ";
        $to = readline();
        echo "Amount: ";
        $amount = readline();

        $this->bank->doTransactionWithAccNumber($from, $to, $amount);
    }

    function deposit(){
        echo "Please enter the data:\n";
        echo "Account number: ";
        $account = readline();
        echo "Amount: ";
        $amount = readline();
        $this->bank->doDeposit($account, $amount);
    }

    function debit(){
        echo "Please enter the data:\n";
        echo "Account number: ";
        $account = readline();
        echo "Amount: ";
        $amount = readline();
        $this->bank->doDebit($account, $amount);
    }

    function showBalance(){
        echo "Please enter the data:\n";
        echo "Account number: ";
        $account = readline();
        echo "Balance of " . $account .": " . $this->bank->getBalance($account) ."\n";
    }

    function addInterest(){
        echo "Please enter the data:\n";
        echo "Account number: ";
        $account = readline();
        echo "Balance after adding interest: " . $this->bank->addInterest($account) ."\n";
    }

    function showAll(){
        $bankAccounts = $this->bank->getBankAccounts();
        print_r("AccNr | CustNr | Balance\n");
        foreach ($bankAccounts as $account){
            print_r($account->getAccountNumber()."  |   ".$account->getCustomer()->getCustomerNumber()."    |    ".$account->getBalance()."\n");
        }
    }
}