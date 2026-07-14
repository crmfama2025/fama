<?php

namespace App\Services\Investment;

use App\Models\Investment;
use App\Models\InvestmentContractDocuments;
use App\Models\InvestorAgreementType;
use App\Repositories\Investment\InvestmentContractDocumentRepository;
use App\Repositories\Investment\InvestorAgreementRepository;
use App\Services\PdfCompressionService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class InvestmentContractDocumentService
{
    public function __construct(
        protected InvestmentContractDocumentRepository $investmentContractDocumentRepository,
        protected InvestorAgreementRepository $InvestorAgreementRepository,
    ) {}


    public function getAll()
    {
        return $this->investmentContractDocumentRepository->all();
    }

    public function getById($id)
    {
        return $this->investmentContractDocumentRepository->find($id);
    }

    // public function getByName($name)
    // {
    //     return $this->investmentContractDocumentRepository->getByName($name);
    // }

    public function create(array $data, $user_id = null)
    {
        $data['added_by'] = auth()->user()->id;
        $record = $this->investmentContractDocumentRepository->create($data);
        return $record;
    }

    public function update($id, array $data)
    {
        $data['updated_by'] = auth()->user()->id;
        $record = $this->investmentContractDocumentRepository->update($id, $data);
        return $record;
    }

    // public function delete($id)
    // {
    //     return $this->investmentContractDocumentRepository->delete($id);
    // }


    private function validate(array $data, $id = null)
    {
        $validator = Validator::make($data, [

            'investment_contract_file_name' => 'required|string|max:255',
            'investment_contract_file_path' => 'required|string|max:500',
        ], [

            'investment_contract_file_name.required' => 'Contract file name is required.',
            'investment_contract_file_name.string' => 'Contract file name must be a string.',
            'investment_contract_file_name.max' => 'Contract file name cannot exceed 255 characters.',
            'investment_contract_file_path.required' => 'Contract file path is required.',
            'investment_contract_file_path.string' => 'Contract file path must be a string.',
            'investment_contract_file_path.max' => 'Contract file path cannot exceed 500 characters.',

        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
    public function getDataTable(array $filters = [])
    {
        $query = $this->investmentContractDocumentRepository->getQuery($filters);

        $columns = [
            ['data' => 'DT_RowIndex', 'name' => 'id'],
            ['data' => 'company_name', 'name' => 'company.company_name'],
            // ['data' => 'investment_code', 'name' => 'investment.investment_code'],
            ['data' => 'investor_name', 'name' => 'investor.investor_name'],
            ['data' => 'investor_agreement_type', 'name' => 'investor_agreement_types.investor_agreement_type'],
            ['data' => 'investor_name', 'name' => 'investor.investor_name'],
            ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false],
        ];

        return datatables()
            ->of($query)
            ->addIndexColumn()
            ->addColumn('company_name', fn($row) => $row->company->company_name ?? '-')
            ->addColumn(
                'invested_company_name',
                fn($row) =>
                $row->investedCompany->company_name ?? '-'
            )
            ->addColumn('investor_name', fn($row) => $row->investor->investor_name . " - " . $row->investor->investor_code ?? '-')
            ->addColumn('company_name', fn($row) =>
            $row->company->company_name ?? '-')
            // ->addColumn('investment_code', fn($row) =>
            // $row->investment->investment_code ?? '-')
            ->addColumn('investor_agreement_type', fn($row) => $row->agreementType->investor_agreement_type)
            ->addColumn('investor_agreement_template', fn($row) => 'V' . $row->agreementTemplate->version_no)
            ->addColumn('status', function ($row) {
                if (!empty($row->generated_date)) {
                    return '<span class="badge badge-success">Generated</span>';
                } else {
                    return '<span class="badge badge-warning">Pending</span>';
                }
            })
            // Main Document View
            ->addColumn('main_doc_view', function ($row) {
                if ($row->contract_file_path) {
                    return '<a href="' . Storage::url($row->contract_file_path) . '"
                    target="_blank"
                    class="btn btn-sm btn-outline-primary"
                    title="View Document">
                    <i class="fas fa-eye"></i>
                </a>';
                }
                return '-';
            })

            // Additional Document View
            ->addColumn('additional_doc_view', function ($row) {
                if ($row->additional_file_path) {
                    return '<a href="' . Storage::url($row->additional_file_path) . '"
                    target="_blank"
                    class="btn btn-sm btn-outline-info"
                    title="View Document">
                    <i class="fas fa-eye"></i>
                </a>';
                }
                return '-';
            })

            ->addColumn('generated_date', function ($row) {
                return $row->generated_date
                    ? \Carbon\Carbon::parse($row->generated_date)->format('d M Y h:i A')
                    : '-';
            })


            ->addColumn('action', function ($row) use ($filters) {
                $action = '';

                $action .= '<a href="' . route('investment.document', $row->id) . '"
                            class="btn btn-sm btn-warning m-1"
                            title="Documents">
                            <i class="fas fa-file-upload"></i>
                        </a>';
                // if ($row->generated_date) {
                //     $action .= '<a href="' . route('investment.document.view', $row->id) . '"
                //             class="btn btn-sm btn-primary m-1"
                //             title="Documents">
                //             <i class="fas fa-eye"></i>
                //         </a>';
                // }

                $action .= '<a href="' . route('legal_template.contractview', [
                    'docId' => $row->id,
                    'companyId' => $row->company_id,
                ]) . '"
                                    class="btn btn-sm btn-success m-1"
                                    title="View Document">
                                 <i class="fas fa-external-link-alt"></i>
                                </a>';


                return $action;
            })
            ->rawColumns(['action', 'investor_agreement_type', 'main_doc_view', 'additional_doc_view', 'generated_date', 'investor_agreement_template', 'status'])
            ->toJson();
    }
    public function documentsFormData()
    {
        $data['doc_types'] = InvestorAgreementType::all();
        return $data;
    }
    public function getDetails($id)
    {
        return $this->investmentContractDocumentRepository->getDetails($id);
    }
    public function updateDocument($data, $id)
    {
        $document = InvestmentContractDocuments::find($id);

        if (!$document) {
            return false;
        }

        $investor   = $document->investor;
        $investment = $document->investment;

        //folder (COMMON for both)
        $folder = 'investments/'
            . $investor->investor_code . '/'
            . $document->company->company_name . '/investments/'
            . $document->short_codes;

        $pdfService = new PdfCompressionService();

        /*
    |--------------------------------------------------
    | MAIN DOCUMENT
    |--------------------------------------------------
    */
        if (!empty($data['document']) && $data['document'] instanceof \Illuminate\Http\UploadedFile) {

            // delete old
            if ($document->contract_file_path && \Storage::disk('public')->exists($document->contract_file_path)) {
                \Storage::disk('public')->delete($document->contract_file_path);
            }

            $fileName = time() . '_main_' . $data['document']->getClientOriginalName();

            if ($data['document']->getClientOriginalExtension() === 'pdf') {

                $path = $pdfService->compress(
                    $data['document'],
                    $folder,
                    $fileName
                );
            } else {

                $path = $data['document']->storeAs(
                    $folder,
                    $fileName,
                    'public'
                );
            }

            $document->contract_file_path = $path;
        }


        /*
    |--------------------------------------------------
    | ADDITIONAL DOCUMENT
    |--------------------------------------------------
    */
        if (!empty($data['additional_document']) && $data['additional_document'] instanceof \Illuminate\Http\UploadedFile) {

            // delete old
            if ($document->additional_file_path && \Storage::disk('public')->exists($document->additional_file_path)) {
                \Storage::disk('public')->delete($document->additional_file_path);
            }

            $fileName = time() . '_additional_' . $data['additional_document']->getClientOriginalName();

            if ($data['additional_document']->getClientOriginalExtension() === 'pdf') {

                $path = $pdfService->compress(
                    $data['additional_document'],
                    $folder,
                    $fileName
                );
            } else {

                $path = $data['additional_document']->storeAs(
                    $folder,
                    $fileName,
                    'public'
                );
            }

            $document->additional_file_path = $path;
        }


        // $document->generated_date = !empty($data['generated_date'])
        //     ? \Carbon\Carbon::parse($data['generated_date'])->format('Y-m-d H:i:s')
        //     : null;
        $document->generated_date = !empty($data['generated_date'])
            ? \Carbon\Carbon::parse($data['generated_date'])->setTimeFrom(now())
            : null;
        // dd($document->generated_date);

        $document->has_additional_doc = $data['has_additional_doc'] ?? 0;
        $document->action_type       = $data['action_type'] ?? null;
        $document->generated_by      = auth()->id();

        $document->save();

        return $document;
    }
    public function createInvestorDocument($investorId, $companyId, $docInsertData)
    {
        // dump('createInvestorDocument');
        // dump($investorId);
        // dump($docInsertData);
        // dd($docInsertData);
        $docInsertData['generated_date'] = now()->format('Y-m-d H:i:s');
        $docInsertData['generated_by'] = auth()->user()->id;

        $lastMudarabah = InvestmentContractDocuments::where('investor_id', $investorId)
            ->where('company_id', $companyId)
            ->where('investor_agreement_type_id', 1) // Mudarabah
            ->latest('id') // or latest('created_at')
            ->first();

        $docInsertData['reference_mudarabah_id'] = $lastMudarabah ? $lastMudarabah->id : null;
        // dd($docInsertData);
        if ($docInsertData['investment_id'] == 0) {
            if ($docInsertData['investor_agreement_type_id'] == 3) {
                return $this->createAgreement($docInsertData, $companyId, 3); //Partial Withdrawal
            }

            $this->createAgreement($docInsertData, $companyId, 4); // Novation
            $this->createAgreement($docInsertData, $companyId, 1); // Mudarabah

        } else {

            if (isset($docInsertData['investor_agreement_type_id'])) {
                if ($docInsertData['investor_agreement_type_id'] == 5) {
                    return  $this->createAgreement($docInsertData, $companyId, 5);
                }
            } else {
                $companyInvestmentCount = Investment::where('investor_id', $investorId)
                    ->where('company_id', $companyId)
                    ->count();

                if ($companyInvestmentCount == 1) {
                    return $this->createAgreement($docInsertData, $companyId, 1); // Mudarabah
                } elseif ($companyInvestmentCount > 1) {


                    return $this->createAgreement($docInsertData, $companyId, 2); // Addendum
                }
            }
        }
    }

    private function createAgreement(array $data, int $companyId, int $agreementTypeId)
    {
        $data['company_id'] = $companyId;
        $data['investor_agreement_type_id'] = $agreementTypeId;
        $data['investor_agreement_template_id'] = $this->InvestorAgreementRepository
            ->getActiveIdBytype($agreementTypeId);

        return $this->create($data);
    }

    public function updateInvestorDocument($investorId, $companyId, $docInsertData)
    {
        // dd("test");
        $companyInvestmentCount = Investment::where('investor_id', $investorId)
            ->where('company_id', $companyId)
            ->count();
        $contract_doc = InvestmentContractDocuments::where('investment_id', $docInsertData['investment_id'])
            ->whereIn('investor_agreement_type_id', [1, 2])
            ->first();
        // dd($companyInvestmentCount);
        if (
            $companyInvestmentCount == 1
        ) {
            // Mudaraba contract
            $docInsertData['company_id'] = $companyId;
            $docInsertData['investor_agreement_type_id'] = 1;
            $docInsertData['investor_agreement_template_id'] = $this->InvestorAgreementRepository->getActiveIdBytype($docInsertData['investor_agreement_type_id']);
            // dd($docInsertData);
            return $this->update($contract_doc->id, $docInsertData);
        } elseif ($companyInvestmentCount > 1) {
            // Addendum contract
            $docInsertData['company_id'] = $companyId;
            $docInsertData['investor_agreement_type_id'] = 2;
            $docInsertData['investor_agreement_template_id'] = $this->InvestorAgreementRepository->getActiveIdBytype($docInsertData['investor_agreement_type_id']);
            // dd($docInsertData);
            return $this->update($contract_doc->id, $docInsertData);
        }
    }
}
