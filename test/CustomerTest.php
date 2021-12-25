<?php
require __DIR__ . "/../src/models/Customer.php";

use models\Customer;
use PHPUnit\Framework\TestCase;

class CustomerTest extends TestCase
{
    private Customer $customer;

    public function setUp() : void {
        $this->customer = new Customer(100, 'Max', 'Mustermann', 'Musterstraße 1, 88888 Musterstadt', new \DateTime('01.01.2000'));
    }

    public function testGetCustomerNumber()
    {
        $this->assertEquals(100, $this->customer->getCustomerNumber());
    }

    public function testSetCustomerNumber(): void
    {
        $this->customer->setCustomerNumber(200);
        $this->assertEquals(200, $this->customer->getCustomerNumber());
    }
}