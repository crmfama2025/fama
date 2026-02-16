<?php

namespace App\Services;

use App\Models\Area;
use App\Repositories\CompanyRepository;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CompanyService
{
    protected $companyRepository;

    public function __construct(CompanyRepository $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    public function getAll($module = null, $submodule = null)
    {
        return $this->companyRepository->all($module, $submodule);
    }

    public function getById($id)
    {
        return $this->companyRepository->find($id);
    }

    public function getIdByCompanyname(string $companyName): ?string
    {
        return $this->companyRepository->findId(['company_name' => $companyName])?->id;
    }

    public function getByData($companyName)
    {
        return $this->companyRepository->getByData($companyName);
    }

    public function createOrRestore(array $data, $user_id = null)
    {

        $data['added_by'] = $user_id ? $user_id : auth()->user()->id;
        $data['company_code'] = $this->setCompanyCode();
        // dd($data);
        $this->validate($data);

        $existing = $this->companyRepository->checkIfExist($data);

        if ($existing) {
            if ($existing->trashed()) {
                $existing->restore();
            }
            $existing->fill($data);
            $existing->save();
            return $existing;
        }

        return $this->companyRepository->create($data);
    }

    public function update($id, array $data)
    {
        $this->validate($data, $id);
        $data['updated_by'] = auth()->user()->id;
        return $this->companyRepository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->companyRepository->delete($id);
    }

    public function setCompanyCode($addval = 1)
    {
        $codeService = new \App\Services\CodeGeneratorService();
        return $codeService->generateNextCode('companies', 'company_code', 'CMP', 5, $addval);
    }

    private function validate(array $data, $id = null)
    {
        $validator = Validator::make($data, [
            'company_name' => [
                'required',
                Rule::unique('companies', 'company_name')
                    ->ignore($id)
                    ->where(fn($q) => $q->whereNull('deleted_at'))
            ],
            'email' => 'required|email',
            'industry_id' => 'required|exists:industries,id',
            'phone' => [
                'required',
                'numeric',
                'regex:/^[1-9][0-9]{9,14}$/'
            ],
            'company_short_code' => [
                'required',
                Rule::unique('companies', 'company_short_code')
                    ->ignore($id)
                    ->where(fn($q) => $q->whereNull('deleted_at'))
            ]
        ], [

            'industry_id.required' => 'Please select an industry.'
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    public function checkIfExist($data)
    {
        return $this->companyRepository->checkIfExist($data);
    }

    public function getDataTable(array $filters = [])
    {
        $query = $this->companyRepository->getQuery($filters);

        $columns = [
            ['data' => 'DT_RowIndex', 'name' => 'id'],
            ['data' => 'company_name', 'name' => 'company_name'],
            ['data' => 'company_code', 'name' => 'company_code'],
            ['data' => 'company_short_code', 'name' => 'company_short_name'],
            ['data' => 'industry', 'name' => 'industry'],
            ['data' => 'address', 'name' => 'address'],
            ['data' => 'phone', 'name' => 'phone'],
            ['data' => 'email', 'name' => 'email'],
            ['data' => 'website', 'name' => 'website'],
            ['data' => 'action', 'name' => 'action', 'orderable' => true, 'searchable' => true],
        ];

        return datatables()
            ->of($query)
            ->addIndexColumn()
            ->addColumn('company_name', fn($row) => ucfirst($row->company_name) ?? '-')
            ->addColumn('company_code', fn($row) => ucfirst($row->company_code) ?? '-')
            ->addColumn('company_short_code', fn($row) => ucfirst($row->company_short_code) ?? '-')
            ->addColumn('industry', fn($row) => $row->industries_name ?? '-')
            ->addColumn('address', fn($row) => $row->address ?? '-')
            ->addColumn('phone', fn($row) => $row->phone ?? '-')
            ->addColumn('email', fn($row) => $row->email ?? '-')
            ->addColumn('website', fn($row) => $row->website ?? '-')

            ->addColumn('action', function ($row) {
                $action = '<div class="d-flex flex-column flex-md-row ">';
                if (Gate::allows('company.edit')) {
                    $action .= '<button class="btn btn-info mb-1 mr-md-1" data-toggle="modal"
                                                       data-target="#modal-company"
                                                         data-row=\'' .  json_encode($row)  . '\'>Edit</button>';
                }
                if (Gate::allows('company.view')) {
                    $action .= '<a href="' . route('company.show', $row->id) . '" class="btn btn-warning mb-1 mr-md-1">View</a>';
                }
                if (Gate::allows('company.delete')) {
                    $action .= '<button class="btn btn-danger mb-1" onclick="deleteConf(' . $row->id . ')" type="submit">Delete</button>';
                }
                $action .= '</div>';

                return $action ?: '-';
            })
            ->rawColumns(['action'])
            ->with(['columns' => $columns]) // send columns too
            ->toJson();
    }
    public function getWithIndustry($module = null, $submodule = null)
    {
        return $this->companyRepository->getWithIndustry($module, $submodule);
    }
}
