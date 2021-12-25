<?php

namespace ra\kp\models;

class Customer
{
    private int $customerNumber;
    private string $firstName;
    private string $lastName;
    private string $address;
    private \DateTime $birthday;

    /**
     * @param string $firstName
     * @param string $lastName
     * @param string $address
     * @param \DateTime $birthday
     */
    public function __construct(int $customerNumber, string $firstName, string $lastName, string $address, \DateTime $birthday)
    {
        $this->customerNumber = $customerNumber;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->address = $address;
        $this->birthday = $birthday;
    }

    /**
     * @return int
     */
    public function getCustomerNumber(): int
    {
        return $this->customerNumber;
    }

    /**
     * @param int $customerNumber
     */
    public function setCustomerNumber(int $customerNumber): void
    {
        $this->customerNumber = $customerNumber;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    /**
     * @return \DateTime
     */
    public function getBirthday(): \DateTime
    {
        return $this->birthday;
    }

    /**
     * @param \DateTime $birthday
     */
    public function setBirthday(\DateTime $birthday): void
    {
        $this->birthday = $birthday;
    }


}