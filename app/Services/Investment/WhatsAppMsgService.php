<?php

namespace App\Services\Investment;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppMsgService
{
    protected string $apiUrl;
    protected string $apiKey;

    public function __construct()
    {
        // Correct Whatchimp API endpoint for template messages
        $this->apiUrl = "https://app.whatchimp.com/api/v1/whatsapp/send/template";
        $this->apiKey = config('services.whatchimp.api_key');
    }

    /**
     * Send a WhatsApp template message via Whatchimp
     *
     * @param string $phone      Recipient phone with country code (no +)
     * @param string $template   Approved template name
     * @param array  $variables  Ordered array of template variables
     * @param string $language   Template language (default 'en')
     * @return array
     */
    // public function sendTemplateById(string $phone, string $templateId): array
    // {
    //     try {
    //         // normalize phone
    //         $phone = preg_replace('/[^0-9]/', '', $phone);

    //         $payload = [
    //             'apiToken'        => env('WHATCHIMP_API_KEY'),
    //             'phone_number_id' => env('WHATSAPP_NUMBER_ID'),
    //             'template_id'     => $templateId,
    //             'phone_number'    => $phone,
    //         ];

    //         $response = Http::asForm()->post(
    //             'https://app.whatchimp.com/api/v1/whatsapp/send/template',
    //             $payload
    //         );

    //         $data = $response->json();

    //         \Log::info('Whatchimp Template Response', [
    //             'payload' => $payload,
    //             'response' => $data
    //         ]);

    //         return $data;
    //     } catch (\Exception $e) {
    //         \Log::error('Whatchimp Template Error', [
    //             'error' => $e->getMessage()
    //         ]);

    //         return [
    //             'status' => 'error',
    //             'message' => $e->getMessage()
    //         ];
    //     }
    // }
    public function sendTemplateById(array $payload): array
    {
        try {
            // $phone = preg_replace('/[^0-9]/', '', $phone);
            $response = Http::asForm()->post(
                'https://app.whatchimp.com/api/v1/whatsapp/send/template',
                $payload
            );

            $data = $response->json();

            \Log::info('Whatchimp Template Response', [
                'payload'  => $payload,
                'response' => $data
            ]);
            // dd(
            //     $response->status(),
            //     $response->body(),
            //     $response->json()
            // );

            return $data;
        } catch (\Exception $e) {
            \Log::error('Whatchimp Template Error', [
                'error' => $e->getMessage()
            ]);

            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
}
