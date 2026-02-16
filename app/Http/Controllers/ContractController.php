<?php

namespace App\Http\Controllers;

use App\Exports\ContractExport;
use App\Exports\ProjectScopeExport;
use App\Models\Agreement;
use App\Models\Contract;
use App\Models\DocumentType;
use App\Repositories\Agreement\AgreementRepository;
use App\Services\AreaService;
use App\Services\BankService;
use App\Services\CompanyService;
use App\Services\Contracts\ContractCommentService;
use App\Services\Contracts\ContractService;
use App\Services\Contracts\DocumentService;
use App\Services\Contracts\PaymentDetailService;
use App\Services\Contracts\PaymentReceivableService;
use App\Services\Contracts\ProjectScopeDataService;
use App\Services\Contracts\UnitDetailService;
use Illuminate\Http\Request;
use App\Services\InstallmentService;
use App\Services\LocalityService;
use App\Services\PayableClearingService;
use App\Services\PaymentModeService;
use App\Services\PropertyService;
use App\Services\PropertyTypeService;
use App\Services\VendorService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ContractController extends Controller
{
    //
    public function __construct(
        protected PropertyService $propertyService,
        protected CompanyService $companyService,
        protected LocalityService $localityService,
        protected AreaService $areaService,
        protected PropertyTypeService $propertyTypeService,
        protected InstallmentService $installmentService,
        protected VendorService $vendorService,
        protected ContractService $contractService,
        protected UnitDetailService $udetSev,
        protected PaymentDetailService $paymentSev,
        protected PaymentReceivableService $paymentRecSev,
        protected ProjectScopeDataService $scopeService,
        protected DocumentService $documentService,
        protected AgreementRepository $agreementRepo,
        protected UnitDetailService $unitdetServ,
        protected ContractCommentService $commentservice,
        protected PaymentModeService $paymentModeService,
        protected BankService $bankService,
        protected PayableClearingService $payableServ,
    ) {}

    public function index()
    {
        $title = 'Contracts';
        $paymentmodes = $this->paymentModeService->getAll();
        $banks = $this->bankService->getAll();
        $companies = $this->companyService->getAll();

        return view("admin.projects.contract.contract", compact("title", "paymentmodes", "banks", "companies"));
    }

    public function create()
    {
        $title = 'Create Contract';
        $contract = null;
        $renew = 0;
        $edit = 0;
        // dropdown values
        $dropdowns = $this->contractService->getDropdownData('add');
        // dd($dropdowns);

        return view("admin.projects.contract.contract-create", compact("title", 'contract', 'renew', 'edit') + $dropdowns);
    }

    public function edit($id)
    {
        $title = 'Edit Contract';
        $contract = $this->contractService->getAllDataById($id);
        $renew = 0;
        $edit = 1;
        // dd($contract->contract_detail);
        $dropdowns = $this->contractService->getDropdownData('edit');

        $indirectct = $this->contractService->getindirect($id);
        $indirectCollection = collect($dropdowns['indirect']);

        if ($indirectct && !$indirectCollection->contains('id', $indirectct->id)) {
            $indirectCollection->push($indirectct);
        }

        $indirectCollection = $indirectCollection->unique('id');

        $dropdowns['indirect'] = $indirectCollection;

        // dd($indirectct);


        return view('admin.projects.contract.contract-create', compact('title', 'contract', 'renew', 'edit', 'indirectct') + $dropdowns);
    }

    public function store(Request $request)
    {
        try {
            // if ($request->contract['id'] != 0) {
            //     $contract = $this->contractService->update($request->contract['id'], $request->all());

            //     return response()->json(['success' => true, 'data' => $contract, 'message' => 'Contract updated successfully'], 200);
            // } else {
            $contract = $this->contractService->createOrRestore($request->all());

            return response()->json(['success' => true, 'data' => $contract, 'message' => 'Contract created successfully'], 201);
            // }
        } catch (\Exception $e) {

            return response()->json(['success' => false, 'message' => $e->getMessage(), 'error'   => $e], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $contract = $this->contractService->update($id, $request->all());

            if ($contract->contract_scope != null) {
                $this->exportBuildingSummary($id, 'update');
            }


            return response()->json(['success' => true, 'data' => $contract, 'message' => 'Contract updated successfully'], 200);
        } catch (\Exception $e) {

            return response()->json(['success' => false, 'message' => $e->getMessage(), 'error'   => $e], 500);
        }
    }

    public function show(Contract $contract)
    {
        $contract = $this->contractService->getById($contract->id);
        $returned = $this->payableServ->getByCondition(['returned_status' => 1, 'contract_id' => $contract->id]);
        $allChildren = $this->contractService->getAllChildren($contract->id);
        // dd($allChildren);
        return view('admin.projects.contract.contract-view', compact('contract', 'allChildren', 'returned'));
    }



    public function getContracts(Request $request)
    {
        if ($request->ajax()) {
            $filters = [
                // 'company_id' => auth()->user()->company_id,
                'search' => $request->search['value'] ?? null
            ];
            return $this->contractService->getDataTable($filters);
        }
    }

    public function destroy(Contract $contract)
    {
        if ($contract->indirect_status == 1) {
            $this->contractService->updateIndirectContract($contract);
        }
        if ($contract->is_indirect_contract == 1) {
            $this->contractService->updateIndirectParent($contract);
        }

        $this->contractService->delete($contract->id);
        return response()->json(['success' => true, 'message' => 'Contract deleted successfully']);
    }

    public function contract_documents($contractId)
    {
        $title = 'Contract Documents';
        $contract = $this->contractService->getById($contractId);
        $documentTypes = DocumentType::where('status', 1)->get();
        $contractDocuments = $this->documentService->getByContractId($contractId);
        $contractUnitdetails = $this->unitdetServ->getByContractId($contractId);

        // dd($agreements);
        return view("admin.projects.contract.contract-documents", compact("title", 'contract', 'documentTypes', 'contractDocuments', 'contractUnitdetails'));
    }

    public function document_upload(Request $request)
    {
        try {
            $this->documentService->uploadDocuments($request->all());
            // dd('success');
            // Continue saving documents...
            return response()->json(['success' => true, 'message' => 'Documents uploaded successfully.'], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // dd($e->errors());
            // Return error to view
            return response()->json(['success' => false, 'message' => $e->errors(), 'error'   => $e], 500);
        }
    }

    public function exportContract(Contract $contract)
    {
        $search = request('search');
        $filters = auth()->user()->company_id ? [
            // 'company_id' => auth()->user()->company_id,
        ] : null;

        return Excel::download(new ContractExport($search, $filters), 'contracts.xlsx');
    }

    public function deleteUnitDetail($id)
    {
        // print($id);
        try {
            $this->udetSev->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Unit detail deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function deletePaymentDetail($id)
    {
        // print($id);
        try {
            $this->paymentSev->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Payment Payable deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function deletePaymentReceivable($id)
    {
        try {
            $this->paymentRecSev->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Payment Payable deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function getRenewalPendingContracts()
    {
        $title = 'Renewal Pendings';

        return view("admin.projects.contract.contract-renewal-list", compact("title"));
    }

    public function getRenewalContractsList(Request $request)
    {
        if ($request->ajax()) {
            $filters = [
                // 'company_id' => auth()->user()->company_id,
                'search' => $request->search['value'] ?? null
            ];
            return $this->contractService->getRenewalDataTable($filters);
        }
    }

    public function renewContracts($contract_id)
    {
        $renew = 1;
        $edit = 0;
        $contract = $this->contractService->getAllDataById($contract_id);
        $title = 'Renew Contract P-' . $contract->project_number;
        $dropdowns = $this->contractService->getDropdownData('renew');

        return view('admin.projects.contract.contract-create', compact('contract', 'renew', 'title', 'edit') + $dropdowns);
    }

    public function rejectRenewal(Request $request, $contract_id)
    {
        try {
            $this->contractService->rejectRenew($request, $contract_id);

            return response()->json([
                'success' => true,
                'message' => 'Renew rejected successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function exportBuildingSummary($id, $stage = null)
    {
        $contract = $this->contractService->getAllDataById($id);
        $file_name = "Project " . $contract->project_number . (($contract->contract_type_id == 1) ? '_Direct' : '') . (($contract->parent_contract_id) ? '_Renewal' : '') . '_' . $contract->property->property_name . ' Building Summary.xlsx';


        // Generate temporary signed URL valid for a few seconds
        if ($stage != null) {
            $downloadUrl = $this->downloadSummary($id, $file_name);
        } else {
            $downloadUrl = route('contract.downloadSummary', [
                'id' => $id,
                'filename' => urlencode($file_name)
            ]);
        }


        // $downloadUrl2 = $this->downloadSummary($id, urlencode($file_name));
        // dd($downloadUrl);
        return response()->json([
            'file_url' => $downloadUrl,
            'redirect_url' => route('contract.index')
        ]);
    }

    public function downloadSummary($contractId, $filename)
    {
        // dump('downloadsummary');
        // return;
        return Excel::download(new ProjectScopeExport($contractId, $this->scopeService), $filename);
    }

    public function downloadScope($id)
    {
        $scopeData = $this->scopeService->getScope($id);
        $binaryExcel = base64_decode($scopeData['scope']);

        // file_put_contents(storage_path('app/test.xlsx'), $binaryExcel);

        // dd($binaryExcel);
        return response()->stream(function () use ($binaryExcel) {
            echo $binaryExcel;
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $scopeData['file_name'] . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }
    public function getTerminatedAgreementDetails($id)
    {
        $agreement = Agreement::where('contract_id', $id)
            ->where('agreement_status', 1)
            ->latest()
            ->first();
        // dd($agreement);

        // Contract details
        $contract = Contract::with('contract_payment_receivables')->find($id);
        $receivables = $contract->contract_payment_receivables;

        $startDate = Carbon::parse(
            $receivables->first()->receivable_date
        );

        $endDate = Carbon::parse(
            $receivables->last()->receivable_date
        );
        if (!$agreement) {
            return;
        }

        $terminatedDate = Carbon::parse($agreement->terminated_date);

        // $remaining = $receivables->filter(function ($r) use ($terminatedDate) {
        //     return Carbon::parse($r->receivable_date)->gt($terminatedDate);
        // });

        // $remainingCount = $remaining->count();

        $remainingReceivables = $receivables
            ->filter(function ($receivable) use ($terminatedDate) {
                return Carbon::parse($receivable->receivable_date)->greaterThan($terminatedDate);
            })
            ->map(function ($receivable) {
                return [
                    'receivable_date' => Carbon::parse($receivable->receivable_date)->format('d-m-Y'),
                    'receivable_amount' => $receivable->receivable_amount,
                ];
            })
            ->values();
        // dd($remainingReceivables);
        $remainingCount = $remainingReceivables->count();
        $remainingAmountSum = $remainingReceivables->sum('receivable_amount');

        // dd([
        //     'receivables' => $receivables,
        //     'start_date' => $startDate->format('Y-m-d'),
        //     'end_date' => $endDate->format('Y-m-d'),
        //     'terminated_date' => $terminatedDate->format('Y-m-d'),
        //     'remaining_installments' => $remainingCount,
        //     'remaining_receivables' => $remainingReceivables
        // ]);

        return response()->json([
            'terminated_agreement' => $agreement,
            'contract_end_date' => $contract?->contract_end_date,
            'receivables' => $receivables,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'terminated_date' => $terminatedDate->format('Y-m-d'),
            'remaining_installments' => $remainingCount,
            'remaining_receivables' => $remainingReceivables,
            'remainingTotal' => $remainingAmountSum
        ]);
    }
    public function checkAgreement(Request $request, $contractId)
    {
        $unitId = $request->unit_id;
        $subunitId = $request->subunit_id;

        $agreement = Agreement::with('agreement_payment_details')
            ->where('agreements.contract_id', $contractId)
            ->where('agreements.agreement_status', 1)
            ->whereHas('agreement_units', function ($query) use ($unitId, $subunitId) {
                $query->where('contract_unit_details_id', $unitId)
                    ->where('contract_subunit_details_id', $subunitId);
            })
            ->latest()
            ->first();
        // dd($agreement);

        $contract = Contract::with('contract_payment_receivables')->find($contractId);
        $receivables = $contract->contract_payment_receivables;

        $remainingReceivables = collect();
        $remainingCount = 0;
        $remainingAmountSum = 0;
        $terminatedDate = null;
        if (!$agreement) {
            return response()->json([
                'exists' => false,
                'agreement' => null,
                'remaining_installments' => 0,
                'remaining_receivables' => [],
                'remainingTotal' => 0,
            ]);
        }

        if ($agreement) {
            $terminatedDate = Carbon::parse($agreement->terminated_date);
            // dd($terminatedDate);

            $remainingReceivables = $receivables
                ->filter(function ($receivable) use ($terminatedDate) {
                    return Carbon::parse($receivable->receivable_date)->greaterThan($terminatedDate);
                })
                ->map(function ($receivable) {
                    return [
                        'receivable_date' => Carbon::parse($receivable->receivable_date)->format('d-m-Y'),
                        'receivable_amount' => $receivable->receivable_amount,
                    ];
                })
                ->values();
            // dd($remainingReceivables);

            $remainingCount = $remainingReceivables->count();
            $remainingAmountSum = $agreement->agreement_payment_details
                ->where('terminated_status', 1)
                ->sum('payment_amount');
        }

        return response()->json([
            'exists' => $agreement ? true : false,
            'agreement' => $agreement,
            'remaining_installments' => $remainingCount,
            'remaining_receivables' => $remainingReceivables,
            'remainingTotal' => $remainingAmountSum
        ]);
    }

    public function contractApproval($contractId)
    {
        $title = 'Contract Approval';
        $contract = $this->contractService->getById($contractId);
        $comments = $this->commentservice->getByContractId($contractId);

        return view('admin.projects.contract.contract-approve', compact('contract', 'title', 'comments'));
    }

    public function sendComments(Request $request)
    {
        try {
            $this->commentservice->create($request->all());
            // dd('success');
            // Continue saving documents...
            return response()->json(['success' => true, 'message' => 'Comments added successfully.'], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // dd($e->errors());
            // Return error to view
            return response()->json(['success' => false, 'message' => $e->errors(), 'error'   => $e], 500);
        }
    }

    public function sendForApproval(Request $request)
    {
        try {
            $this->commentservice->create($request->all());
            // $contract = Contract::findOrFail($request->id);
            // $contract->contract_status = $request->status;
            // $contract->save();

            return response()->json(['success' => true, 'message' => 'Contract send for Approval.'], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // dd($e->errors());
            // Return error to view
            return response()->json(['success' => false, 'message' => $e->errors(), 'error'   => $e], 500);
        }
    }

    public function approveContract(Request $request)
    {
        try {
            $this->contractService->approveContract($request->all());
            // $contract = Contract::findOrFail($request->id);
            // $contract->contract_status = $request->status;
            // $contract->save();

            return response()->json(['success' => true, 'message' => 'Contract send for Approval.'], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // dd($e->errors());
            // Return error to view
            return response()->json(['success' => false, 'message' => $e->errors(), 'error'   => $e], 500);
        }
    }

    // public function rejectContract(Request $request) {}

    public function getComments($contractId)
    {
        $comments = $this->commentservice->getByContractId($contractId);
        return response()->json(['comments' => $comments]);
    }

    public function acknowledgement_view($id)
    {
        $title = "Contract Acknowledgement";
        $contract = $this->contractService->getById($id);
        $page = 1;
        return view('admin.projects.contract.acknowledgement', compact('contract', 'page', 'title'));
    }


    public function acknowledgement_print($id)
    {
        $contract = $this->contractService->getById($id);
        $page = 0;
        $pdf = Pdf::loadView('admin.projects.contract.pdf-acknowledgement', compact('contract', 'page'))
            ->setPaper([0, 0, 830, 1400]);
        return $pdf->stream('contract-' . $contract->id . '.pdf');
    }

    public function release($contractId)
    {
        $this->documentService->updateContractAcknowledgement($contractId);

        return redirect()->route('contracts.acknowledgement', $contractId)
            ->with('success', 'Acknowledgement released');
        // ->back()->with('success', 'Acknowledgement released');
    }


    public function terminate(Request $request)
    {

        $this->contractService->terminateContract($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Contract terminated successfully'
        ]);
    }
    public function updateIndirectData($contract) {}
}
