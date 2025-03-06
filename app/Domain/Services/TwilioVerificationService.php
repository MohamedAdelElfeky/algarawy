<?php

namespace App\Domain\Services;

use App\Domain\Entities\PhoneNumber;
use App\Domain\Entities\OTP;

interface TwilioVerificationService
{
    public function sendOtp(PhoneNumber $phone): bool;
    public function verifyOtp(PhoneNumber $phone, OTP $otp): bool;
}
