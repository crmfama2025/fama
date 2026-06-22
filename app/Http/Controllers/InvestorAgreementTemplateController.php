<?php

namespace App\Http\Controllers;

use App\Models\InvestorAgreementType;
use App\Services\Investment\InvestorAgreementService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;

class InvestorAgreementTemplateController extends Controller
{
    public function __construct(
        protected InvestorAgreementService $invAgreement
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

    public function mudarabah_view($id)
    {
        $page = 9;
        return view('admin.investment.inv_agreement.pdfview-agreement', compact('page'));
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
}
