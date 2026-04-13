<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Models\EmailLog;
use Illuminate\Support\Facades\Log;

class BrevoService
{
    protected $apiKey;
    protected $client;
    protected $senderEmail;
    protected $senderName;

    public function __construct()
    {
        $this->apiKey = config('services.brevo.api_key');
        $this->senderEmail = 'noreply@famacrm.cloud';
        $this->senderName = 'Fama Real Estate';

        $this->client = new Client([
            'base_uri' => 'https://api.brevo.com/v3/',
            'headers' => [
                'api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]
        ]);
    }

    /**
     * Send email via Brevo API
     *
     * @param array $to Array of recipients: [['email'=>'...', 'name'=>'...']]
     * @param string $subject Email subject
     * @param string $bladeViewName Blade template path
     * @param array $viewData Data to pass to Blade template
     * @return string "true" if sent, or error message
     */
    public function sendEmail(array $to, string $subject, string $bladeViewName, array $viewData)
    {
        // dd("test");
        $emailLog = new EmailLog();
        // $emailLog->app = 'Fama Real Estate';

        // Render Blade template as HTML
        $htmlContent = view($bladeViewName, $viewData)->render();

        // Log request
        $emailLog->requested = json_encode([
            "sender" => ["name" => $this->senderName, "email" => $this->senderEmail],
            "to" => $to,
            "subject" => $subject
        ]);
        // dd("test");

        try {
            // Prepare recipients for API
            $recipients = [];
            foreach ($to as $recipient) {
                $recipients[] = [
                    "email" => $recipient['email'],
                    "name" => $recipient['name']
                ];
            }

            $payload = [
                "sender" => [
                    "name" => $this->senderName,
                    "email" => $this->senderEmail
                ],
                "to" => $recipients,
                "subject" => $subject,
                "htmlContent" => $htmlContent
            ];

            // dd($payload);

            // Send email
            $response = $this->client->post('smtp/email', ['json' => $payload]);
            // dd($response);

            $body = $response->getBody()->getContents();

            // dd($body);

            // Log response
            $emailLog->response = json_encode([
                "status_code" => $response->getStatusCode(),
                "body" => (string) $response->getBody()
            ]);
            // dd($emailLog);
            $emailLog->save();

            // Brevo returns 201 for success
            return $response->getStatusCode() === 201 ? "true" : "Failed to send email";
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // Handle 403 / activation errors
            $body = $e->getResponse() ? (string) $e->getResponse()->getBody() : $e->getMessage();
            $emailLog->response = json_encode(["error" => $body]);
            $emailLog->save();

            if (strpos($body, 'not yet activated') !== false) {
                return "Your Brevo SMTP/API account is not activated. Contact support to enable sending.";
            }

            return "Something went wrong: " . $body;
        } catch (\Exception $e) {
            $emailLog->response = json_encode(["error" => $e->getMessage()]);
            $emailLog->save();

            return "Something went wrong: " . $e->getMessage();
        }
    }
}
