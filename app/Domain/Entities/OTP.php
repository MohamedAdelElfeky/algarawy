<?php

namespace App\Domain\Entities;

class OTP
{
    private string $code;

    public function __construct(string $code)
    {
        $this->validate($code);
        $this->code = $code;
    }

    private function validate(string $code): void
    {
        if (!preg_match('/^\d{4,6}$/', $code)) {
            throw new \InvalidArgumentException("Invalid OTP format.");
        }
    }

    public function getValue(): string
    {
        return $this->code;
    }
}
