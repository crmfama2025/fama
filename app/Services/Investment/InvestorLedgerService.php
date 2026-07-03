<?php

namespace App\Services\Investment;

use App\Models\Investment;
use App\Models\InvestmentContractDocuments;
use App\Models\InvestorAgreementType;
use App\Models\InvestorLedger;
use App\Repositories\Investment\investorLedgerRepository;
use App\Services\PdfCompressionService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class InvestorLedgerService
{
    public function __construct(
        protected InvestorLedgerRepository $investorLedgerRepository,
    ) {}


    public function getAll()
    {
        return $this->investorLedgerRepository->all();
    }

    public function getById($id)
    {
        return $this->investorLedgerRepository->find($id);
    }



    public function create(array $data, $user_id = null)
    {
        // dd($data);
        $data['added_by'] = auth()->user()->id;
        // dd($data);
        $record = $this->investorLedgerRepository->create($data);
        return $record;
    }

    public function update($id, array $data)
    {

        $data['updated_by'] = auth()->user()->id;
        $this->investorLedgerRepository->update($id, $data);
        // }
    }

    // public function delete($id)
    // {
    //     return $this->investorLedgerRepository->delete($id);
    // }



    public function getDataTable(array $filters = [])
    {
        $query = $this->investorLedgerRepository->getQuery($filters);

        $columns = [
            ['data' => 'DT_RowIndex', 'name' => 'id'],
            ['data' => 'company_name', 'name' => 'company.company_name'],
            ['data' => 'investment_code', 'name' => 'investment.investment_code'],
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

            ->addColumn('investment_code', fn($row) =>
            $row->investment->investment_code ?? '-')
            ->addColumn('investor_agreement_type', fn($row) => $row->agreementType->investor_agreement_type)
            ->addColumn('investor_agreement_template', fn($row) => 'V' . $row->investor_agreement_template_id)
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
                    'docType' => 1,
                    'companyId' => $row->investment->company_id,
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

    public function getDetails($id)
    {
        return $this->investorLedgerRepository->getDetails($id);
    }


    public function createInvestmentLedger($investorId, $companyId, $investor, $ledgerInsertData, $investment)
    {
        $companyInvestmentCount = Investment::where('investor_id', $investorId)
            ->where('company_id', $companyId)
            ->count();
        if (
            $companyInvestmentCount == 1
        ) {
            // Mudaraba contract
            $ledgerInsertData['investor_transaction_type_id'] = 1;
            $ledgerInsertData['is_credit'] = 1;
            $ledgerInsertData['company_id'] = $companyId;
            $ledgerInsertData['company_id'] = $companyId;
            $ledgerInsertData['transaction_date'] = $investment->investment_date;
            $ledgerInsertData['transaction_amount'] = $investment->investment_amount;
            $this->create($ledgerInsertData);
        } elseif ($companyInvestmentCount > 1) {
            // Addendum contract
            $ledgerInsertData['investor_transaction_type_id'] = 2;
            $ledgerInsertData['is_credit'] = 1;
            $ledgerInsertData['company_id'] = $companyId;
            $ledgerInsertData['transaction_date'] = $investment->investment_date;
            $ledgerInsertData['transaction_amount'] = $investment->investment_amount;
            $this->create($ledgerInsertData);
        }
    }
    public function updateInvestmentLedger($investorId, $companyId, $investor, $ledgerInsertData, $investment)
    {
        $companyInvestmentCount = Investment::where('investor_id', $investorId)
            ->where('company_id', $companyId)
            ->count();
        $ledger = InvestorLedger::where('investment_id', $investment->id)
            ->whereIn('investor_transaction_type_id', [1, 2])
            ->first();
        if (
            $companyInvestmentCount == 1
        ) {
            // Mudaraba contract
            $ledgerInsertData['investor_transaction_type_id'] = 1;
            $ledgerInsertData['is_credit'] = 1;
            $ledgerInsertData['company_id'] = $companyId;
            $ledgerInsertData['company_id'] = $companyId;
            $ledgerInsertData['transaction_date'] = $investment->investment_date;
            $ledgerInsertData['transaction_amount'] = $investment->investment_amount;
            $this->update($ledger->id, $ledgerInsertData);
        } elseif ($companyInvestmentCount > 1) {
            // Addendum contract
            $ledgerInsertData['investor_transaction_type_id'] = 2;
            $ledgerInsertData['is_credit'] = 1;
            $ledgerInsertData['company_id'] = $companyId;
            $ledgerInsertData['transaction_date'] = $investment->investment_date;
            $ledgerInsertData['transaction_amount'] = $investment->investment_amount;

            $this->update($ledger->id, $ledgerInsertData);
        }
    }
}
