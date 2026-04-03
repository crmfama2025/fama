<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FcmService
{
    public function sendNotification($tokens, $title, $body, $id)
    {
        if (empty($tokens)) return;

        $accessToken = getFcmAccessToken();
        // dd("test");


        foreach ($tokens as $token) {
            $message = [
                "message" => [
                    "token" => $token,

                    "webpush" => [
                        "notification" => [
                            "title" => $title,
                            "body" => $body,
                            "icon" => url('/images/favicon.png'),
                            // "click_action" => route('contract.show', $id)
                        ],
                        "data" => ["link" => route('contract.show', $id)],
                        "fcm_options" => [
                            "link" => route('contract.show', $id) // open on click
                        ]
                    ],
                    "android" => ["priority" => "high", "notification" => ["sound" => "default"]],
                    "apns" => ["headers" => ["apns-priority" => "10"], "payload" => ["aps" => ["sound" => "default"]]]
                ]
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/v1/projects/fama-10735/messages:send', $message);
            // $response = Http::withHeaders([
            //     'Authorization' => 'Bearer ' . $token,
            //     'Content-Type' => 'application/json',
            // ])->post('https://fcm.googleapis.com/v1/projects/fama-10735/messages:send', $message);
            // dd($response);

            \Log::info('FCM Response: ' . $response->body());
        }
    }
}
