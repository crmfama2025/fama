<?php

namespace App\Services\Investment;

use App\Models\Investment;
use App\Models\InvestmentContractDocuments;
use App\Models\InvestorAgreementType;
use App\Models\InvestorLedger;
use App\Repositories\Investment\InvestorLedgerRepository;
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
        // dd("test");
        $query = $this->investorLedgerRepository->getQuery($filters);

        $columns = [
            ['data' => 'DT_RowIndex', 'name' => 'id'],
            ['data' => 'company_name', 'name' => 'company.company_name'],
            ['data' => 'investment_code', 'name' => 'investment.investment_code'],
            ['data' => 'investor_name', 'name' => 'investor.investor_name'],
            ['data' => 'transaction_type', 'name' => 'transactionType.transaction_type'],
            ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false],

        ];

        return datatables()
            ->of($query)
            ->addIndexColumn()

            ->addColumn('investor_name', fn($row) => $row->investor->investor_name . " - " . $row->investor->investor_code ?? '-')
            ->addColumn('company_name', fn($row) => $row->investment->company->company_name  ?? '-')
            ->addColumn('transaction_amount', fn($row) => $row->transaction_amount  ?? '-')
            ->addColumn('transaction_date', function ($row) {
                return $row->transaction_date
                    ? \Carbon\Carbon::parse($row->transaction_date)->format('d M Y h:i A')
                    : '-';
            })

            ->addColumn('investment_code', fn($row) =>
            $row->investment->investment_code ?? '-')
            ->addColumn('transaction_type', function ($row) {

                $typeId = $row->investor_transaction_type_id;
                $typeName = $row->transactionType->transaction_type ?? 'N/A';

                $badges = [
                    1 => 'success',
                    2 => 'info',
                    3 => 'warning',
                    4 => 'maroon',
                ];

                $class = $badges[$typeId] ?? 'secondary';

                return "<span class='badge badge-{$class}'>{$typeName}</span>";
            })
            ->addColumn('credit_debit', function ($row) {

                if ($row->is_credit) {
                    return "<span class='badge badge-success'>
                    <i class='fas fa-arrow-down mr-1'></i> Credit
                </span>";
                } else {
                    return "<span class='badge badge-danger'>
                    <i class='fas fa-arrow-up mr-1'></i> Debit
                </span>";
                }
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
            ->rawColumns(['action', 'transaction_type', 'credit_debit', 'transaction_amount', 'transaction_date'])
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
            ->where('investment_term_type', 1)
            ->count();
        if (
            $companyInvestmentCount == 1 || $investment->investment_term_type == 2
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
    public function updateInvestmentLedger($investorId, $companyId, $ledgerInsertData, $investment)
    {
        $companyInvestmentCount = Investment::where('investor_id', $investorId)
            ->where('company_id', $companyId)
            ->where('investment_term_type', 1)
            ->count();
        $ledger = InvestorLedger::where('investment_id', $investment->id)
            ->whereIn('investor_transaction_type_id', [1, 2])
            ->first();
        if (
            $companyInvestmentCount == 1 || $investment->investment_term_type == 2
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
