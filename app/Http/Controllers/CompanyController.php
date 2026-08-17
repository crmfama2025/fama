<?php

namespace App\Http\Controllers;

use App\Exports\CompanyExport;
use App\Models\Company;
use App\Models\Industry;
use App\Services\CompanyService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class CompanyController extends Controller
{
    //
    public function __construct(
        protected CompanyService $companyService,
    ) {}
    public function index()
    {
        $title = 'Companies';
        $industries = Industry::all();
        // dd($industries);

        return view("admin.company.company", compact("title", "industries"));
    }
    public function store(Request $request)
    {
        // dd($request->all());
        try {
            if ($request->id != 0) {
                $company = $this->companyService->update($request->id, $request->all());

                return response()->json(['success' => true, 'data' => $company, 'message' => 'Company updated successfully'], 200);
            } else {
                $company = $this->companyService->createOrRestore($request->all());

                return response()->json(['success' => true, 'data' => $company, 'message' => 'Company created successfully'], 201);
            }
        } catch (\Exception $e) {

            return response()->json(['success' => false, 'message' => $e->getMessage(), 'error'   => $e], 500);
        }
    }

    public function getCompanies(Request $request)
    {
        if ($request->ajax()) {
            $filters = [
                'company_id' => auth()->user()->company_id,
                'search' => $request->search['value'] ?? null
            ];
            return $this->companyService->getDataTable($filters);
        }
    }
    public function destroy(Company $company)
    {
        $this->companyService->delete($company->id);
        return response()->json(['success' => true, 'message' => 'Company deleted successfully']);
    }

    public function exportCompany(Company $company)
    {
        $search = request('search');
        $filters = auth()->user()->company_id ? [
            'company_id' => auth()->user()->company_id,
        ] : null;

        return Excel::download(new CompanyExport($search, $filters), 'companies.xlsx');
    }
    public function show(Company $company)
    {
        return view('admin.company.company-view', compact('company'));
    }
}
