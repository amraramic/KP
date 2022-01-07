<?php

namespace ra\kp\models;

class Customer
{
    private int $customerNumber;
    private string $firstName;
    private string $lastName;

    /**
     * @param int $customerNumber
     * @param string $firstName
     * @param string $lastName
     */
    public function __construct(int $customerNumber, string $firstName, string $lastName)
    {
        $this->customerNumber = $customerNumber;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    /**
     * @return int
     */
    public function getCustomerNumber(): int
    {
        return $this->customerNumber;
    }


    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }
}