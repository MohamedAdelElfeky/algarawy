<?php

namespace App\Applications\UseCases;

use App\Domain\Services\TwilioVerificationService;
use App\Domain\Entities\PhoneNumber;
use App\Domain\Entities\OTP;
use App\Applications\DTOs\OTPVerificationDTO;

class VerifyOTPUseCase
{
    protected TwilioVerificationService $twilioService;

    public function __construct(TwilioVerificationService $twilioService)
    {
        $this->twilioService = $twilioService;
    }

    public function execute(OTPVerificationDTO $dto): bool
    {
        $phone = new PhoneNumber($dto->phone);
        $otp = new OTP($dto->otp);
        
        return $this->twilioService->verifyOtp($phone, $otp);
    }
}
