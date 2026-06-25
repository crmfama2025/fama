<?php

namespace App\Services\Investment;

use App\Repositories\Investment\InvestorAgreementRepository;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class InvestorAgreementService
{
    public function __construct(
        protected InvestorAgreementRepository $InvAgreementRepo,
    ) {}

    private function validate(array $data, $id = null)
    {
        $validator = Validator::make($data, [
            'investor_agreement_type_id' => 'required',
            'version_no' => [
                'required',
                Rule::unique('investor_agreement_templates')
                    ->where(function ($query) use ($data) {
                        return $query->where(
                            'investor_agreement_type_id',
                            $data['investor_agreement_type_id']
                        );
                    })
                    ->ignore($id)
            ],
            'effective_from' => 'required',
            'is_active' => 'required'
        ], [
            'version_no.unique' => 'This version already exists for the selected document type.'
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    public function create(array $data)
    {
        $this->validate($data);
        $data['added_by'] = auth()->user()->id;

        return $this->InvAgreementRepo->create($data);
    }

    public function update($id, array $data)
    {
        $this->validate($data, $id);
        $data['updated_by'] = auth()->user()->id;

        return $this->InvAgreementRepo->update($id, $data);
    }

    public function getDataTable(array $filters = [])
    {
        $query = $this->InvAgreementRepo->getQuery($filters);

        $columns = [
            ['data' => 'DT_RowIndex', 'name' => 'iddd', 'title' => '#'],
            ['data' => 'agreement_type', 'name' => 'agreement_type', 'title' => 'Type'],
            ['data' => 'version_no', 'name' => 'version_no', 'title' => 'Version No'],
            ['data' => 'effective_from', 'name' => 'effective_from', 'title' => 'Effective From'],
            ['data' => 'is_active', 'name' => 'is_active', 'title' => 'Status'],
            ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false],
        ];
        return datatables()
            ->of($query)
            ->addIndexColumn()
            ->addColumn('agreement_type', function ($row) {
                return $row->agreementType->investor_agreement_type ?? '-';
            })
            ->addColumn('version_no', function ($row) {
                return 'V' . $row->version_no ?? '-';
            })
            ->addColumn('effective_from', function ($row) {
                return $row->effective_from;
            })
            ->addColumn('is_active', function ($row) {
                return $row->is_active;
            })

            ->addColumn('action', function ($row) {
                $action = '<div class="d-flex flex-column flex-md-row ">';
                if (Gate::allows('investor_legal_documents.edit')) {
                    $action .= '<a href="' . route('legal_template.edit', $row->id) . '" class="btn btn-info btn-sm mb-1 mr-md-1" >Edit</a>';
                }

                if (Gate::allows('investor_legal_documents.view')) {
                    $action .= '<a href="' . route('legal_template.show', $row->id) . '" class="btn btn-primary btn-sm mb-1 mr-md-1" >View</a>';
                }
                $action .= '</div>';

                return $action;
            })
            ->rawColumns(['is_active', 'action'])
            ->with(['columns' => $columns])
            ->toJson();
    }

    public function getById($id)
    {
        return $this->InvAgreementRepo->findById($id);
    }
}
