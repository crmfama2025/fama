<?php

namespace App\Http\Controllers;

use App\Services\Agreement\AgreementService;
use Illuminate\Http\Request;

class InvestorContractTemplateController extends Controller
{
    public function __construct(
        protected AgreementService $agreementService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = 'Investor Contract Template';

        return view("admin.investment.investor-contract-template", compact("title"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function print_view($id)
    {
        $agreement = $this->agreementService->getDetails($id);
        $page = 1;
        return view('admin.investment.contract-content.mudarabah', compact('agreement', 'page'));
    }

    public function print($id)
    {
        // $agreement = $this->agreementService->getDetails($id);
        // $page = 0;
        // $pdf = Pdf::loadView('admin.projects.agreement.pdf-agreement', compact('agreement', 'page'))
        //     ->setPaper([0, 0, 930, 1250]);
        // return $pdf->stream('agreement-' . $agreement->id . '.pdf');
    }
}
