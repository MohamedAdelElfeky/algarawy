<?php

namespace App\Infrastructure\Services;

use Twilio\Rest\Client;
use App\Domain\Services\TwilioVerificationService;
use App\Domain\Entities\PhoneNumber;
use App\Domain\Entities\OTP;

class TwilioService implements TwilioVerificationService
{
    protected Client $twilio;
    protected string $twilioPhoneNumber;

    public function __construct()
    {
        $this->twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
        $this->twilioPhoneNumber = env('TWILIO_FROM');
    }

    public function sendOtp(PhoneNumber $phone): bool
    {
        try {
            $this->twilio->verify->v2->services(env('TWILIO_VERIFY_SID'))
                ->verifications
                ->create($phone->getValue(), "sms");

            return true;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function verifyOtp(PhoneNumber $phone, OTP $otp): bool
    {
        try {
            $verificationCheck = $this->twilio->verify->v2->services(env('TWILIO_VERIFY_SID'))
                ->verificationChecks
                ->create([
                    'to' => $phone->getValue(),
                    'code' => $otp->getValue()
                ]);

            return $verificationCheck->status === 'approved';
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
    /**
     * Send an SMS message to a user
     * 
     * @param PhoneNumber $phone
     * @param string $message
     * @return bool
     * @throws \Exception
     */
    public function sendMessage(PhoneNumber $phone, string $message): bool
    {
        try {
            $this->twilio->messages->create(
                $phone->getValue(),
                [
                    'from' => $this->twilioPhoneNumber,
                    // 'from' => env('TWILIO_MESSAGE_SID'),
                    'messagingServiceSid' => env('TWILIO_MESSAGE_SID'),
                    'body' => $message
                ]
            );

            return true;
        } catch (\Exception $e) {
            \Log::error("فشل التحقق من الرقم: " . $e->getMessage());
            return false;
        }
    }
}
