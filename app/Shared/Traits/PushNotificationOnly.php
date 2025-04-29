<?php

namespace App\Shared\Traits;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Illuminate\Support\Facades\Log;

trait PushNotificationOnly
{
    protected $messaging;

    public function __construct()
    {
        $factory = (new Factory)->withServiceAccount(storage_path('app/firebase/serviceAccountKey.json'));
        $this->messaging = $factory->createMessaging();
    }

    public function sendFCMNotificationToUsers(array $users, string $title, string $body, array $dataPayload): void
    {
        foreach ($users as $user) {
            foreach ($user->devices as $device) {
                if ($device->notification_token) {
                    $cleanedData = $this->sanitizeData($dataPayload + ['user_id' => $user->id]);
                    $this->sendFCM($device->notification_token, $title, $body, $cleanedData);
                }
            }
        }
    }

    private function sanitizeData(array $data): array
    {
        return array_map(function ($value) {
            if (is_object($value)) {
                return method_exists($value, 'toArray')
                    ? json_encode($value->toArray(request()))
                    : (string) $value;
            } elseif (is_array($value)) {
                return json_encode($value);
            }
            return (string) $value;
        }, $data);
    }


    public function sendFCM(string $token, string $title, string $body, array $data = []): void
    {
        if (!$this->messaging) {
            $factory = (new Factory)->withServiceAccount(storage_path('app/firebase/serviceAccountKey.json'));
            $this->messaging = $factory->createMessaging();
        }

        $message = CloudMessage::withTarget('token', $token)
            ->withNotification(Notification::create($title, $body))
            ->withData($data);

        try {
            $this->messaging->send($message);
        } catch (\Exception $e) {
            Log::error('FCM Error: ' . $e->getMessage());
        }
    }

    // public function sendFCM(string $token, string $title, string $body, array $data = []): void
    // {
    //     $SERVER_API_KEY = env('FCM_SERVER_KEY');

    //     $headers = [
    //         'Authorization: key=' . $SERVER_API_KEY,
    //         'Content-Type: application/json',
    //     ];

    //     $payload = [
    //         "registration_ids" => [$token],
    //         "notification" => [
    //             "title" => $title,
    //             "body" => $body,
    //             "sound" => "default",
    //             "click_action" => "FLUTTER_NOTIFICATION_CLICK",
    //         ],
    //         "data" => $data + ["click_action" => "FLUTTER_NOTIFICATION_CLICK"]
    //     ];

    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    //     curl_setopt($ch, CURLOPT_POST, true);
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //     curl_exec($ch);
    //     curl_close($ch);
    // }
}
