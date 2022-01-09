<?php


use ra\kp\exceptions\InvalidAmountException;
use ra\kp\models\BankAccount;
use PHPUnit\Framework\TestCase;
use ra\kp\models\CheckingAccount;
use ra\kp\models\Customer;

class BankAccountTest extends TestCase
{
    protected BankAccount $bankAccount;
    protected function setUp(): void
    {
        $customer = new Customer(
            "1",
            "Max",
            "Mustermann",
        );

        $this->bankAccount = $this->getMockForAbstractClass(BankAccount::class, [
            "accountNumber" => "100",
            "customer" => $customer,
            "balance" => 100]
        );
    }

    /**
     * @throws InvalidAmountException
     */
    public function testDebit()
    {
        $this->bankAccount->debit(30);
        $this->assertEquals(70, $this->bankAccount->getBalance());

        $this->expectException(InvalidAmountException::class);
        $this->bankAccount->debit(100);
    }

    /**
     * @throws InvalidAmountException
     */
    public function testDeposit()
    {
        $this->bankAccount->deposit(50);
        $this->assertEquals(150, $this->bankAccount->getBalance());

        $this->expectException(InvalidAmountException::class);
        $this->bankAccount->deposit(0);
    }
}
