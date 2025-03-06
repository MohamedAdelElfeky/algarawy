<?php

namespace App\Applications\DTOs;

class OTPVerificationDTO
{
    public string $phone;
    public string $otp;

    public function __construct(string $phone, string $otp)
    {
        $this->phone = $phone;
        $this->otp = $otp;
    }
}
