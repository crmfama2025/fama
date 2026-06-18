<?php

namespace App\Services;

use App\Imports\PaymentModeImport;
use App\Repositories\NationalityRepository;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class NationalityService
{

    public function __construct(
        protected NationalityRepository $nationalityRepository,
        protected CompanyService $companyService,
    ) {}

    public function getAll()
    {
        return $this->nationalityRepository->all();
    }

    public function getById($id)
    {
        return $this->nationalityRepository->find($id);
    }

    public function createOrRestore(array $data, $user_id = null)
    {
        $this->validate($data);
        $data['added_by'] = $user_id ? $user_id : auth()->user()->id;
        $data['nationality_code'] = $this->setPaymentModeCode();

        $existing = $this->nationalityRepository->checkIfExist($data);

        if ($existing) {
            if ($existing->trashed()) {
                $existing->restore();
            }
            $existing->fill($data);
            $existing->save();
            return $existing;
        }

        return $this->nationalityRepository->create($data);
    }

    public function update($id, array $data)
    {
        $this->validate($data, $id);
        $data['updated_by'] = auth()->user()->id;
        return $this->nationalityRepository->updateOrRestore($id, $data);
    }

    public function delete($id)
    {
        return $this->nationalityRepository->delete($id);
    }

    public function setPaymentModeCode($addval = 1)
    {
        $codeService = new \App\Services\CodeGeneratorService();
        return $codeService->generateNextCode('nationalities', 'nationality_code', 'NCT', 5, $addval);
    }

    private function validate(array $data, $id = null)
    {
        $validator = Validator::make($data, [
            'nationality_name' => [
                'required',
                Rule::unique('nationalities')->ignore($id)
                    ->where(fn($q) => $q
                        // ->where('company_id', $data['company_id'])
                        ->whereNull('deleted_at'))
            ],
            'nationality_short_code' => 'required',
            // 'company_id' => 'required|exists:companies,id',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    public function getDataTable(array $filters = [])
    {
        $query = $this->nationalityRepository->getQuery($filters);

        $columns = [
            ['data' => 'DT_RowIndex', 'name' => 'id'],
            // ['data' => 'company_name', 'name' => 'company_name'],
            ['data' => 'nationality_name', 'name' => 'nationality_name'],
            ['data' => 'nationality_arabic_name', 'name' => 'nationality_arabic_name'],
            ['data' => 'nationality_short_code', 'name' => 'nationality_short_code'],
            ['data' => 'action', 'name' => 'action', 'orderable' => true, 'searchable' => true],
        ];

        return datatables()
            ->of($query)
            ->addIndexColumn()
            // ->addColumn('company_name', fn($row) => $row->company->company_name ?? '-')
            ->addColumn('nationality_name', fn($row) => $row->nationality_name ?? '-')
            ->addColumn('nationality_arabic_name', fn($row) => $row->nationality_arabic_name ?? '-')
            ->addColumn('nationality_short_code', fn($row) => $row->nationality_short_code ?? '-')
            ->addColumn('action', function ($row) {
                $action = '<div class="d-flex flex-column flex-md-row ">';
                if (Gate::allows('nationality.edit')) {
                    $action .= '<button class="btn btn-info mb-1 mr-md-1" data-toggle="modal"
                                                        data-target="#modal-nationality"
                                                        data-row=\'' .  json_encode($row)  . '\'>Edit</button>';
                }
                if (Gate::allows('nationality.delete')) {
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
        $rows = Excel::toCollection(new PaymentModeImport, $file)->first();

        $insertData = [];
        $reestoreCount = 0;
        foreach ($rows as $key => $row) {
            // print_r($row);
            // $company_id = $this->companyService->getIdByCompanyname($row['company']);

            // if ($company_id == null) {
            //     $existing = $this->companyService->checkIfExist(array('company_name' => $row['company'], 'nationality_name' => $row['country_name']));

            //     if (!empty($existing)) {
            //         // echo "exist";
            //         $existing->restore();

            //         $company_id = $existing->id;
            //     } else {
            //         $company_id = $this->companyService->createOrRestore([
            //             'company_name' => $row['company'],
            //         ], $user_id)->id;
            //     }
            // }

            $nationalityexist = $this->nationalityRepository->checkIfExist(array('nationality_name' => $row['country_name'])); //, 'company_id' => $company_id
            if ($nationalityexist) {
                if ($nationalityexist->trashed()) {
                    $nationalityexist->restore();
                    $reestoreCount++;
                }
            } else {
                $insertData[] = [
                    // 'company_id' => $company_id,
                    'nationality_code' => $this->setPaymentModeCode($key + 1),
                    'nationality_name' => $row['country_name'],
                    'nationality_short_code' => $row['country_code'],
                    'created_at' => now(),
                    'updated_at' => now(),
                    'added_by' => $user_id,
                ];
            }

            // if (empty($paymentModeexist)) {
            //     $insertData[] = [
            //         // 'company_id' => $company_id,
            //         'nationality_code' => $this->setPaymentModeCode($key + 1),
            //         'nationality_name' => $row['country_name'],
            //         'nationality_short_code' => $row['country_code'],
            //         'created_at' => now(),
            //         'updated_at' => now(),
            //         'added_by' => $user_id,
            //     ];
            // }
        }

        $this->nationalityRepository->insertBulk($insertData);

        // return count($insertData);
        return ['inserted' => count($insertData), 'restored' => $reestoreCount];
    }
}
