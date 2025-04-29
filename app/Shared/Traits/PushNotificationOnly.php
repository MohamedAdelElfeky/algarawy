<?php

namespace App\Shared\Traits;

trait PushNotificationOnly
{
    public function sendFCMNotificationToUsers(array $users, string $title, string $body, array $dataPayload): void
    {
        foreach ($users as $user) {
            foreach ($user->devices as $device) {
                if ($device->notification_token) {
                    $this->sendFCM($device->notification_token, $title, $body, $dataPayload + ['user_id' => $user->id]);
                }
            }
        }
    }


    public function sendFCM(string $token, string $title, string $body, array $data = []): void
    {
        $SERVER_API_KEY = env('FCM_SERVER_KEY');

        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];

        $payload = [
            "registration_ids" => [$token],
            "notification" => [
                "title" => $title,
                "body" => $body,
                "sound" => "default",
                "click_action" => "FLUTTER_NOTIFICATION_CLICK",
            ],
            "data" => $data + ["click_action" => "FLUTTER_NOTIFICATION_CLICK"]
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_exec($ch);
        curl_close($ch);
    }
}
