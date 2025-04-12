<?php

namespace App\Actions;

use App\Domain\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SendNotification
{
    public static function to_user($receiver_id, $info, $url = '')
    {
        Notification::create([
            'sender_id'   => Auth::id(),
            'receiver_id' => $receiver_id,
            'ar_content'  => $info['ar'],
            'en_content'  => $info['en'],
            'url'         => $url,
            'seen'        => '0',
        ]);

        self::send($receiver_id, $info['ar']);
    }

    public static function send($id, $info, $from = 'Algarawy Notification')
    {
        $user = User::with('devices')->find($id);

        if ($user) {
            $SERVER_API_KEY = 'BIPBc-T545O_2ylKY3YKZCIKQD_jcUs7nVPOF0fhcLxnSaFFmL25UXTU3_yVE-phbr7x80rdpJ2AVfVRFRZq34c';
            $headers = [
                'Authorization: key=' . $SERVER_API_KEY,
                'Content-Type: application/json',
            ];

            foreach ($user->devices->whereNotNull('notification_token') as $device) {
                $token = $device->notification_token;

                $data = [
                    "registration_ids" => [$token],
                    "data" => ['type' => 'activation', 'click_action' => 'FLUTTER_NOTIFICATION_CLICK'],
                    "notification" => [
                        "title" => $from,
                        "body" => $info,
                        "sound" => "default",
                        "click_action" => "FLUTTER_NOTIFICATION_CLICK",
                    ],
                ];

                $dataString = json_encode($data);

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

                $response = curl_exec($ch);

                if (curl_errno($ch)) {
                    \Log::error('FCM Error: ' . curl_error($ch));
                }

                curl_close($ch);
            }
        }

        return true;
    }
}
