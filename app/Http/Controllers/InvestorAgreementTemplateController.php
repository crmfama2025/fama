<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateAndSendSignedAgreementPdf;
use App\Models\AgreementSignatureEvent;
use App\Models\InvestmentContractDocuments;
use App\Models\InvestorAgreementType;
use App\Services\Investment\AgreementSignatureService;
use App\Services\Investment\InvestmentContractService;
use App\Services\Investment\InvestmentService;
use App\Services\Investment\InvestorAgreementService;
use App\Services\Investment\InvestorService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;

class InvestorAgreementTemplateController extends Controller
{
    public function __construct(
        protected InvestorAgreementService $invAgreement,
        protected InvestmentContractService $invContractServ,
        protected AgreementSignatureService $signatureService,
        protected InvestorService $investorService,
        protected InvestmentService $investmentService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = 'Investor Legal Template';

        return view("admin.investment.inv_agreement.investor-agreement-template", compact("title"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Investor Legal Template Versioning';

        $investorTemplate = null;
        $invAgreements = InvestorAgreementType::all();

        return view("admin.investment.inv_agreement.investor-agreement", compact("title", "invAgreements", "investorTemplate"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            $investor = $this->invAgreement->create($request->all());

            return response()->json(['success' => true, 'data' => $investor, 'message' => 'Investor template created successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'error'   => $e], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $template = $this->invAgreement->getById($id);

        return view('admin.investment.inv_agreement.view-investor-agreement', compact('template'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $title = 'Investor Legal Template Versioning';

        $investorTemplate = $this->invAgreement->getById($id);
        $invAgreements = InvestorAgreementType::all();

        return view("admin.investment.inv_agreement.investor-agreement", compact("title", "invAgreements", "investorTemplate"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $investor = $this->invAgreement->update($request->template_id, $request->all());

            return response()->json(['success' => true, 'data' => $investor, 'message' => 'Investor template updated successfully'], 200);
        } catch (\Exception $e) {

            return response()->json(['success' => false, 'message' => $e->getMessage(), 'error'   => $e], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function contract_view($docId, $companyId)
    {
        $data = $this->invContractServ->sendContractDocument($docId, $companyId);
        // dd($data);
        $contractDocument = InvestmentContractDocuments::find($docId);
        // $contractDocument->investment_id = 0;
        $investment = null;
        if ($contractDocument->investment_id != 0) {
            $investment = $this->investmentService->getById($contractDocument->investment_id);
        }
        // dd($investment);
        // dd($contractDocument);
        $investor = $this->investorService->getById($contractDocument->investor_id);
        $investments = $this->investorService->getCompanyTotalInvestments($contractDocument->investor_id);

        if (!$contractDocument->is_investor_signed) {
            $signerRole = 'investor';
        } else {
            $signerRole = 'company';
        }

        return view('admin.investment.inv_agreement.pdfview-agreement-dynamic', compact('data', 'contractDocument', 'signerRole', 'investor', 'investments', 'investment'));
    }

    public function doc_view()
    {
        return view('admin.investment.inv_agreement.pdfview-agreement');
    }

    public function getInvestorAgreements(Request $request)
    {
        if ($request->ajax()) {
            $filters = [
                'search' => $request->search['value'] ?? null
            ];
            return $this->invAgreement->getDataTable($filters);
        }
    }

    public function signAgreement(Request $request, InvestmentContractDocuments $contract)
    {

        $validated = $request->validate([
            'signer_role'      => 'required|in:investor,company',
            'signature_count'  => 'required|integer|min:1',
            'signed_html'      => 'required|string',
        ]);

        if ($validated['signer_role'] === 'investor' && $contract->is_investor_signed) {
            return response()->json(['message' => 'Investor has already signed.'], 422);
        }
        if ($validated['signer_role'] === 'company' && $contract->is_company_signed) {
            return response()->json(['message' => 'Company has already signed.'], 422);
        }
        // dd($request->all());

        $rawSignature = $request->signature; // full data URL: "data:image/png;base64,iVBORw0KG..."

        // Strip prefix ONLY for decoding to file bytes
        $base64Data = $rawSignature;
        if (str_contains($base64Data, ',')) {
            $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
        }

        if ($validated['signer_role'] === 'investor') {
            $fileName = 'Investors/' . $contract->investor->investor_code . '/signature/' . Str::random(8) . '-' . $contract->id . '.png';
        } else {
            $fileName = 'companies/' . $contract->company->company_name . '/signature/' . Str::random(8) . '-' . $contract->id . '.png';
        }

        Storage::disk('public')->put(
            $fileName,
            base64_decode($base64Data)
        );

        $imageUrl = asset('storage/' . $fileName);

        // Replace the FULL original data URL (with prefix) in the HTML — not the stripped version
        $html = str_replace($rawSignature, $imageUrl, $validated['signed_html']);

        $dom = new \DOMDocument();
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $xpath = new \DOMXPath($dom);

        // Remove all remove buttons
        foreach ($xpath->query("//button[contains(@class,'sig-placed-remove')]") as $btn) {
            $btn->parentNode->removeChild($btn);
        }

        $html = $dom->saveHTML();
        // dd($html);

        $expectedCount = $this->expectedSignatureCountFor($contract, $validated['signer_role']);

        if ($validated['signature_count'] < $expectedCount) {
            return response()->json([
                'message' => "Incomplete signing. Expected {$expectedCount} signature(s), received {$validated['signature_count']}."
            ], 422);
        }
        // dump($html);
        // dd($contract);
        DB::transaction(function () use ($contract, $validated, $imageUrl, $html) {
            $contract->contract_document_html = $html;

            if ($validated['signer_role'] === 'investor') {
                $contract->is_investor_signed = true;
                $contract->investor_signed_at = now();
                $contract->investor_sign  = $imageUrl;
            } else {
                $contract->is_company_signed = true;
                $contract->company_sign  = $imageUrl;
                $contract->company_signed_at = now();
            }

            $contract->updated_by = auth()->id();
            $contract->sign_token = null; // invalidate the signing link after successful signing
            // dd($contract->toArray());
            $contract->save();

            AgreementSignatureEvent::create([
                'contract_id' => $contract->id,
                'signer_role' => $validated['signer_role'],
                'event_type' => 'signed',
                'channel' => 'web',
                'occurred_at' => now(),
            ]);

            // Fully executed once both parties have signed — company signs last
            // if ($validated['signer_role'] === 'company' && $contract->is_investor_signed) {
            //     // dd("test");
            //     GenerateAndSendSignedAgreementPdf::dispatch($contract->id)->afterCommit();
            // }
        });

        return response()->json(['message' => 'Signed successfully.', 'status' => 'success']);
    }

    private function expectedSignatureCountFor(InvestmentContractDocuments $contract, string $signerRole): int
    {
        // Use the ORIGINAL unsigned template — never the client's own signed_html —
        // otherwise a stripped-down payload could under-report and pass its own check.
        $sourceHtml = $contract->contract_document_html; // unsigned source, before this submission

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $sourceHtml);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);

        // Count named slots belonging to this signer (data-signer="investor"/"company")
        $namedSlots = $xpath->query("//*[@data-signature-slot][@data-signer='{$signerRole}']");
        $namedCount = $namedSlots->length;

        if ($namedCount > 0) {
            return $namedCount;
        }

        // No named slots defined at all for this role — fall back to "one per page"
        // (matches the JS default-page behavior for investor-wide initialing)
        if ($signerRole === 'investor') {
            $pages = $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' new-page ')]");
            return max($pages->length, 1);
        }

        return 1; // company: at least one signature expected if no named slots found
    }

    public function investorContractView($uniqueId, $docId)
    {

        // dd(substr(md5(103), 0, 8));
        // Str::random(8)

        // 65b9eea6
        // LEyWFMhF
        // dd($docId);
        // dd($uniqueId);

        $contractDocument = $contractDocument = InvestmentContractDocuments::whereRaw(
            'SUBSTRING(MD5(id), 1, 8) = ?',
            [$docId]
        )->where('sign_token', $uniqueId)
            ->first();
        // dd($contractDocument);
        if (!$contractDocument) {
            abort(404, 'Contract not found or invalid unique key.');
        }

        $data = $this->invContractServ->sendContractDocument($contractDocument->id, $contractDocument->company_id);

        if (!$contractDocument->is_investor_signed) {
            $signerRole = 'investor';
        } else {
            $signerRole = 'company';
        }
        // dd($signerRole);

        // else {
        //     $signerRole = null;
        // }

        $investor = $this->investorService->getById($contractDocument->investor_id);
        $investments = $this->investorService->getCompanyTotalInvestments($contractDocument->investor_id);

        return view('admin.investment.inv_agreement.pdfview-agreement-dynamic', compact('data', 'contractDocument', 'signerRole', 'investor', 'investments'));
    }

    /**
     * Staff triggers sending the signing link (auth required — wire this route inside your auth middleware group).
     */
    public function sendForSignature(Request $request, InvestmentContractDocuments $contract)
    {
        $validated = $request->validate([
            'channel' => 'required|in:whatsapp,email',
        ]);

        $this->signatureService->sendForSignature($contract->id, $validated['channel']);

        return response()->json(['message' => 'Sent for signature via ' . $validated['channel'] . '.']);
    }
}
