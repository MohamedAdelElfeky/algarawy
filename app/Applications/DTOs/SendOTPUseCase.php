<?php

namespace App\Application\UseCases;

use App\Domain\Services\TwilioVerificationService;
use App\Domain\Entities\PhoneNumber;

class SendOTPUseCase
{
    protected TwilioVerificationService $twilioService;

    public function __construct(TwilioVerificationService $twilioService)
    {
        $this->twilioService = $twilioService;
    }

    public function execute(string $phone): bool
    {
        $phoneNumber = new PhoneNumber($phone);
        return $this->twilioService->sendOtp($phoneNumber);
    }
}
