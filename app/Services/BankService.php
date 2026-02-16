<?php

namespace App\Services;

use App\Imports\BankImport;
use App\Repositories\BankRepository;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class BankService
{

    public function __construct(
        protected BankRepository $bankRepository,
        protected CompanyService $companyService,
    ) {}

    public function getAll()
    {
        return $this->bankRepository->all();
    }

    public function getById($id)
    {
        return $this->bankRepository->find($id);
    }

    public function createOrRestore(array $data, $user_id = null)
    {
        $this->validate($data);
        $data['added_by'] = $user_id ? $user_id : auth()->user()->id;
        $data['bank_code'] = $this->setBankCode();

        $existing = $this->bankRepository->checkIfExist($data);

        if ($existing) {
            if ($existing->trashed()) {
                $existing->restore();
            }
            $existing->fill($data);
            $existing->save();
            return $existing;
        }

        return $this->bankRepository->create($data);
    }

    public function update($id, array $data)
    {
        $this->validate($data, $id);
        $data['updated_by'] = auth()->user()->id;
        return $this->bankRepository->updateOrRestore($id, $data);
    }

    public function delete($id)
    {
        return $this->bankRepository->delete($id);
    }

    public function setBankCode($addval = 1)
    {
        $codeService = new \App\Services\CodeGeneratorService();
        return $codeService->generateNextCode('banks', 'bank_code', 'BNK', 5, $addval);
    }

    private function validate(array $data, $id = null)
    {
        $validator = Validator::make($data, [
            'bank_name' => [
                'required',
                Rule::unique('banks')->ignore($id)
                    ->where(
                        fn($q) =>
                        $q
                            ->where('company_id', $data['company_id'])
                            ->whereNull('deleted_at')
                    )
            ],
            'bank_short_code' => 'required',
            'company_id' => 'required|exists:companies,id',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    public function getDataTable(array $filters = [])
    {
        $query = $this->bankRepository->getQuery($filters);

        $columns = [
            ['data' => 'DT_RowIndex', 'name' => 'id'],
            ['data' => 'company_name', 'name' => 'company_name'],
            ['data' => 'bank_name', 'name' => 'bank_name'],
            ['data' => 'back_short_code', 'name' => 'back_short_code'],
            ['data' => 'action', 'name' => 'action', 'orderable' => true, 'searchable' => true],
        ];

        return datatables()
            ->of($query)
            ->addIndexColumn()
            ->addColumn('company_name', fn($row) => $row->company->company_name ?? '-')
            ->addColumn('bank_name', fn($row) => $row->bank_name ?? '-')
            ->addColumn('back_short_code', fn($row) => $row->back_short_code ?? '-')
            ->addColumn('status', fn($row) => $row->status ?? '-')
            ->addColumn('action', function ($row) {
                $action = '<div class="d-flex flex-column flex-md-row ">';
                if (auth()->user()->hasAnyPermission(['bank.edit'], $row->company_id)) {
                    $action .= '<button class="btn btn-info mb-1 mr-md-1" data-toggle="modal"
                                                        data-target="#modal-bank"
                                                        data-row=\'' .  json_encode($row)  . '\'>Edit</button>';
                }
                if (auth()->user()->hasAnyPermission(['bank.view'], $row->company_id)) {
                    $action .= '<a href="' . route('bank.show', $row->id) . '" class="btn btn-warning mb-1 mr-md-1">View</a>';
                }
                if (auth()->user()->hasAnyPermission(['bank.delete'], $row->company_id)) {
                    $action .= '<button class="btn btn-danger mb-1" onclick="deleteConf(' . $row->id . ')" type="submit">Delete</button>';
                }
                $action .= '</div>';

                return $action ?: '-';
            })
            ->rawColumns(['action'])
            ->with(['columns' => $columns]) // send columns too
            ->toJson();
    }

    public function importExcel($file, $user_id)
    {
        // Read Excel as collection
        $rows = Excel::toCollection(new BankImport, $file)->first();

        $insertData = [];
        $restoreCount = 0;
        foreach ($rows as $key => $row) {
            // dd($row);
            $company_id = $this->companyService->getIdByCompanyname($row['company']);

            if ($company_id == null) {
                $existing = $this->companyService->checkIfExist(array('company_name' => $row['company'], 'bank_name' => $row['bank_name']));

                if (!empty($existing)) {
                    // echo "exist";
                    $existing->restore();

                    $company_id = $existing->id;
                } else {
                    $company_id = $this->companyService->createOrRestore([
                        'company_name' => $row['company'],
                    ], $user_id)->id;
                }
            }

            $bankexist = $this->bankRepository->checkIfExist(array('bank_name' => $row['bank_name'], 'company_id' => $company_id));
            if ($bankexist) {
                if ($bankexist->trashed()) {
                    $bankexist->restore();
                    $restoreCount++;
                }
            } else {
                $insertData[] = [
                    'company_id' => $company_id,
                    'bank_code' => $this->setBankCode($key + 1),
                    'bank_name' => $row['bank_name'],
                    'bank_short_code' => $row['bank_short_code'],
                    'created_at' => now(),
                    'updated_at' => now(),
                    'added_by' => $user_id,
                ];
            }

            // if (empty($bankexist)) {
            //     $insertData[] = [
            //         'company_id' => $company_id,
            //         'bank_code' => $this->setBankCode($key + 1),
            //         'bank_name' => $row['bank_name'],
            //         'bank_short_code' => $row['bank_short_code'],
            //         'created_at' => now(),
            //         'updated_at' => now(),
            //         'added_by' => $user_id,
            //     ];
            // }
        }

        $this->bankRepository->insertBulk($insertData);

        // return count($insertData);
        return [
            'inserted' => count($insertData),
            'restored' => $restoreCount,
        ];
    }
}
