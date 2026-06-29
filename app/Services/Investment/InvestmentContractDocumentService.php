<?php

namespace App\Services\Investment;

use App\Repositories\Investment\InvestmentContractDocumentRepository;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class InvestmentContractDocumentService
{
    public function __construct(
        protected InvestmentContractDocumentRepository $investmentContractDocumentRepository,
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
        // dd($data);
        $data['added_by'] = auth()->user()->id;
        // dd($data);
        $record = $this->investmentContractDocumentRepository->create($data);
        return $record;
    }

    public function update($id, array $data)
    {
        // dd($data);
        $this->validate($data);
        $data['updated_by'] = auth()->user()->id;
        $existingDoc = $this->investmentContractDocumentRepository->find($id);
        if ($existingDoc && $existingDoc->investment_contract_file_path) {
            if (Storage::disk('public')->exists($existingDoc->investment_contract_file_path)) {
                Storage::disk('public')->delete($existingDoc->investment_contract_file_path);
            }

            $this->investmentContractDocumentRepository->update($id, $data);
        }
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
            $row->investedment->investment_code ?? '-')
            ->addColumn('investor_agreement_type', fn($row) => $row->agreementType->investor_agreement_type)


            ->addColumn('action', function ($row) use ($filters) {
                $action = '';

                $action .= '<a href="' . route('investment.documents', $row->id) . '"
                            class="btn btn-sm btn-warning m-1"
                            title="Documents">
                            <i class="fas fa-file-upload"></i>
                        </a>';

                return $action;
            })
            ->rawColumns(['action', 'investor_agreement_type'])
            ->toJson();
    }
}
