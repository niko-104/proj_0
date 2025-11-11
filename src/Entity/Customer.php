<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Entity;

readonly class Customer
{
    public function __construct(
        public int    $id,
        public string $firstName,
        public string $lastName,
        public string $middleName,
        public string $email
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getMiddleName(): string
    {
        return $this->middleName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
