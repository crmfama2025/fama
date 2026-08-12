<?php

namespace App\Services\Investment;

use App\Models\AgreementSignatureEvent;
use App\Models\ContractDocument;
use App\Models\InvestmentContractDocuments;
use App\Models\WhatsappMessage;
use App\Repositories\Investment\InvestmentContractDocumentRepository;
use App\Services\BrevoService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class AgreementSignatureService
{
    public function __construct(
        protected InvestmentContractDocumentRepository $InvContractDocRepo,
        protected WhatsAppMsgService $whatsApp,
        protected BrevoService $brevoService,
    ) {}
    /**
     * Whose turn it is to sign next.
     */
    public function currentSignerRole($contract)
    {
        return $this->InvContractDocRepo->isInvestorSigned($contract->id) ? 'company' : 'investor';
    }

    /**
     * Dispatch the signing link via WhatsApp or Email, and log the send.
     */
    public function sendForSignature($contract, string $channel): void
    {
        $contract = $this->InvContractDocRepo->find($contract);
        $signerRole = $this->currentSignerRole($contract);

        $contract->investor_sign_channel = $channel;
        $contract->sign_token = str()->random(8);
        $contract->save();

        $this->logEvent($contract, $signerRole, 'sent', $channel);

        $docIdHash = substr(md5($contract->id), 0, 8);
        $signLink = route('legal_template.investorContractView', [
            'uniqueId' => $contract->sign_token,
            'docId' => $docIdHash,
        ]);
        $signLink_whatsap = str_replace('https://famacrm.cloud/', '', $signLink);

        $channel === 'whatsapp'
            ? $this->sendViaWhatsApp($contract, $signerRole, $signLink_whatsap)
            : $this->sendViaEmail($contract, $signerRole, $signLink);
    }

    /**
     * How many signature spots this role must fill, parsed from the
     * document as it existed BEFORE this submission.
     */
   /* public function expectedSignatureCountFor($contract, string $signerRole): int
    {
        $sourceHtml = $contract->contract_document_html;

        if (!$sourceHtml) {
            return 1;
        }

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $sourceHtml);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);

        $namedSlots = $xpath->query("//*[@data-signature-slot][@data-signer='{$signerRole}']");
        $namedCount = $namedSlots->length;

        if ($namedCount > 0) {
            return $namedCount;
        }

        if ($signerRole === 'investor') {
            $pages = $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' new-page ')]");
            return max($pages->length, 1);
        }

        return 1;
    } */

    /**
     * Persist the signed HTML + signature image, flip flags, log the event.
     */
    /*public function recordSignature(
        $contract,
        string $signerRole,
        string $signedHtml,
        string $rawSignatureDataUrl,
        ?int $updatedBy
    ): string {
        $imageUrl = $this->storeSignatureImage($contract, $signerRole, $rawSignatureDataUrl);

        $html = str_replace($rawSignatureDataUrl, $imageUrl, $signedHtml);
        $html = $this->stripInteractiveElements($html);

        $usedChannel = $contract->sign_channel;

        DB::transaction(function () use ($contract, $signerRole, $html, $imageUrl, $updatedBy, $usedChannel) {
            $contract->contract_document_html = $html;

            if ($signerRole === 'investor') {
                $contract->is_investor_signed = true;
                $contract->investor_signed_at = now();
                $contract->investor_sign = $imageUrl;
            } else {
                $contract->is_company_signed = true;
                $contract->company_signed_at = now();
                $contract->company_sign = $imageUrl;
            }

            $contract->updated_by = $updatedBy;
            $contract->save();

            $this->logEvent($contract, $signerRole, 'signed', $usedChannel);
        });

        return $imageUrl;
    }

    // ── Private helpers ─────────────────────────────────────────────

    private function storeSignatureImage($contract, string $signerRole, string $rawSignatureDataUrl): string
    {
        $base64Data = $rawSignatureDataUrl;
        if (str_contains($base64Data, ',')) {
            $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
        }

        if ($signerRole === 'investor') {
            $fileName = 'Investors/' . $contract->investor->investor_code . '/signature/' . Str::random(8) . '-' . $contract->id . '.png';
        } else {
            $fileName = 'companies/' . $contract->company->company_name . '/signature/' . Str::random(8) . '-' . $contract->id . '.png';
        }

        Storage::disk('public')->put($fileName, base64_decode($base64Data));

        return asset('storage/' . $fileName);
    }

    private function stripInteractiveElements(string $html): string
    {
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);

        foreach ($xpath->query("//button[contains(@class,'sig-placed-remove')]") as $btn) {
            $btn->parentNode->removeChild($btn);
        }
        foreach ($xpath->query("//button[contains(@class,'sig-placeholder-btn')]") as $btn) {
            $btn->parentNode->removeChild($btn);
        }

        return $dom->saveHTML();
    }*/

    private function logEvent($contract, string $signerRole, string $eventType, ?string $channel): void
    {
        AgreementSignatureEvent::create([
            'contract_id' => $contract->id,
            'signer_role' => $signerRole,
            'event_type' => $eventType,
            'channel' => $channel,
            'occurred_at' => now(),
        ]);
    }

    private function sendViaWhatsApp($contract, string $signerRole, string $signLink): void
    {
        // dump($contract->toArray());
        $recipient = $signerRole === 'investor'
            ? $contract->investor->investor_mobile
            : $contract->company->phone;

        // $recipient = '971567623806';
        // dd($recipient);
        $variables = [
            'investor_name' => $contract->investor->investor_name ?? 'Investor',
            'document_name' => $contract->agreementType->investor_agreement_type ?? 'Document',
            'investor_name_ar' => $contract->investor->investor_name_arabic ?? 'Investor',
            'document_name_ar' => document_name_ar($contract->agreementType->short_code) ?? 'Document',
        ];

        $templateId = '407226';
        // -d "templateVariable-investorName-1=investor_name" \
        // -d "templateVariable-documentName-2=document_name" \
        // -d "templateVariable-investorName-3=investor_name_ar" \
        // -d "templateVariable-documentName-4=document_name_ar" \
        $payload = [
            'apiToken' => env('WHATCHIMP_API_KEY'),
            'phone_number_id' => env('WHATSAPP_NUMBER_ID'),
            'template_id' => $templateId,
            'phone_number' => $recipient,
            // Whatchimp variable syntax: templateVariable-<name>-1
            'templateVariable-investorName-1' => $variables['investor_name'],
            'templateVariable-documentName-2' => $variables['document_name'],
            'templateVariable-investorName-3' => $variables['investor_name_ar'],
            'templateVariable-documentName-4' => $variables['document_name_ar'],
            'templateVariable-documentLink-5' => $signLink,
        ];
        $response = $this->whatsApp->sendTemplateById($payload);

        $status = isset($response['status']) && $response['status'] == '1' ? 1 : 0;

        WhatsappMessage::create([
            'investor_id' => $contract->investor_id,
            'phone'       => $recipient,
            'template_id' => $templateId,
            'variables'   => json_encode($variables),
            'payload'     => json_encode($payload),
            'response'    => json_encode($response),
            'status'      => $status,
        ]);

        \Log::info("WhatsApp signature document response", ['response' => $response]);
    }

    private function sendViaEmail($contract, string $signerRole, string $signLink): void
    {

        $recipientEmail = $signerRole === 'investor'
            ? $contract->investor->email
            : $contract->company->email;

        $investorhtml = '<p>
                    Dear ' . $contract->investor->investor_name . ',
                </p>

                <p>Greetings.</p>

                <p>Please find the link to the
                    updated ' . $contract->agreementType->investor_agreement_type . '
                    for
                    your review.</p>

                <p>Kindly review all pages
                    carefully and sign exactly
                    as per your ID using "Add
                    Signature", then use "Place
                    Signature" option to place
                    your signature in all the
                    designated locations
                    throughout before submitting
                    the document.</p>

                <p>Once done, please submit the
                    document.</p>

                <p>If you need any assistance,
                    feel free to contact me.</p>

                <p>Thank you for your
                    cooperation and continued
                    trust.</p>

                <p>Kind Regards,</p>

                <p>عزيزي/تي
                    ' . $contract->investor->investor_name_arabic . '،</p>

                <p>تحية طيبة،</p>

                <p>يرجى مراجعة المستند المُحدّث
                    ' . document_name_ar($contract->agreementType->short_code) . ' من
                    خلال
                    الرابط أدناه.</p>
                <p>يرجى التوقيع بما يطابق
                    التوقيع المعتمد في بطاقة
                    الهوية الإماراتية أو جواز
                    السفر باستخدام خيار "Add
                    Signature"، ثم استخدام
                    "Place Signature" لوضع
                    التوقيع في جميع الأماكن
                    المخصصة، وبعدها الضغط على
                    "Submit" لإرسال المستند.</p>

                <p>في حال احتجتم إلى أي مساعدة،
                    يُرجى عدم التردد في التواصل
                    معنا.</p>

                <p>شكرًا لكم على تعاونكم
                    وثقتكم.</p>

                <p>مع خالص التحية والتقدير،</p>';


        $Companyhtml = '<p>
                    Dear ' . $contract->company->company_name . ',
                </p>

                <p>Greetings.</p>

                <p>Please find the link to the
                    updated ' . $contract->agreementType->investor_agreement_type . '
                    for
                    your review.</p>

                <p>Kindly review all pages
                    carefully and sign using "Add
                    Signature", then use "Place
                    Signature" option to place
                    your signature in all the
                    designated locations
                    throughout before submitting
                    the document.</p>

                <p>Once done, please submit the
                    document.</p>

                <p>Kind Regards,</p>';
        // dd($signerRole);
        if ($signerRole == 'investor') {
            $result = $this->brevoService->sendEmail(
                [
                    // ['email' => 'crmfama2025@gmail.com', 'name' => 'Test User']
                    ['email' => $recipientEmail, 'name' => 'Test User']

                ],
                'Investment Document Signature Request',
                'admin.emails.investment-document-signature-email',
                [
                    // 'name'           => $contract->investor->investor_name,
                    // 'name_ar'           => $contract->investor->investor_name_arabic,
                    // 'document_name'           => $contract->agreementType->investor_agreement_type,
                    // 'document_name_ar'           => document_name_ar($contract->agreementType->short_code),
                    'document_path' => $signLink,
                    'content' => $investorhtml,

                ]
            );
        } else {
            $result = $this->brevoService->sendEmail(
                [
                    ['email' => 'crmfama2025@gmail.com', 'name' => 'Test User']
                ],
                'Investment Document Signature Request',
                'admin.emails.investment-document-signature-email',
                [
                    // 'name'           => $contract->investor->investor_name,
                    // 'name_ar'           => $contract->investor->investor_name_arabic,
                    // 'document_name'           => $contract->agreementType->investor_agreement_type,
                    // 'document_name_ar'           => document_name_ar($contract->agreementType->short_code),
                    'document_path' => $signLink,
                    'content' => $Companyhtml,
                ]
            );
        }

        // Mail::send('emails.sign-agreement', [
        //     'signLink' => $signLink,
        //     'signerRole' => $signerRole,
        //     'contract' => $contract,
        // ], function ($message) use ($recipientEmail) {
        //     $message->to($recipientEmail)
        //         ->subject('Your Investment Agreement Is Ready to Sign');
        // });
    }
}
