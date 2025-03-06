<?php

namespace App\Domain\Entities;

class PhoneNumber
{
    private string $number;

    public function __construct(string $number)
    {
        $this->validate($number);
        $this->number = $number;
    }

    private function validate(string $number): void
    {
        if (!preg_match('/^\+\d{10,15}$/', $number)) {
            throw new \InvalidArgumentException("Invalid phone number format.");
        }
    }

    public function getValue(): string
    {
        return $this->number;
    }
}
