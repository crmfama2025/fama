<?php

namespace App\Http\Controllers;

use App\Exports\BankExport;
use App\Models\Bank;
use App\Services\BankService;
use App\Services\CompanyService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class BankController extends Controller
{
    public function __construct(
        protected BankService $bankService,
        protected CompanyService $companyService,
    ) {}

    public function index()
    {
        $title = 'Banks';
        $companies = $this->companyService->getAll('bank');
        // Company::permittedForModule('bank')->get();

        return view("admin.master.bank", compact("title", "companies"));
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
        try {
            if ($request->id != 0) {
                $bank = $this->bankService->update($request->id, $request->all());

                return response()->json(['success' => true, 'data' => $bank, 'message' => 'Bank updated successfully'], 200);
            } else {
                $bank = $this->bankService->createOrRestore($request->all());

                return response()->json(['success' => true, 'data' => $bank, 'message' => 'Bank created successfully'], 201);
            }
        } catch (\Exception $e) {
            // if ($e->getCode() == 23000) { // integrity constraint violation
            //     throw ValidationException::withMessages([
            //         'bank_name' => 'This bank name already exists for this company.',
            //     ]);
            // } else {
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'error'   => $e], 500);
            // }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Bank $bank)
    {
        //
        return view('admin.master.bank-view', compact('bank'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bank $bank)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bank $bank)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bank $bank)
    {
        $this->bankService->delete($bank->id);
        return response()->json(['success' => true, 'message' => 'Bank soft deleted']);
    }

    public function getBanks(Request $request)
    {
        if ($request->ajax()) {
            $filters = [
                // 'company_id' => auth()->user()->company_id,
                'search' => $request->search['value'] ?? null
            ];
            return $this->bankService->getDataTable($filters);
        }
    }

    public function exportBanks(Request $request)
    {
        $search = request('search');
        $filters = auth()->user()->company_id ? [
            'company_id' => auth()->user()->company_id,
        ] : null;

        return Excel::download(new BankExport($search, $filters), 'banks.xlsx');
    }

    public function importBank(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv'
        ]);

        $file = $request->file('file');

        // Pass a second argument as required by importExcel, e.g., the current user ID or null if not needed
        $result = $this->bankService->importExcel($file, auth()->user()->id);

        // return redirect()->back()->with('success', "$count bank imported successfully.");
        if ($result['inserted'] == 0 && $result['restored'] == 0) {
            return response()->json(['success' => false, 'message' => "No new bank to import."]);
        } else {
            return response()->json(['success' => true, 'message' => "{$result['inserted']} created, {$result['restored']} restored successfully."]);
        }
    }
}
