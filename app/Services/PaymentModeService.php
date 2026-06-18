<?php

namespace App\Services;

use App\Imports\PaymentModeImport;
use App\Repositories\PaymentModeRepository;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class PaymentModeService
{

    public function __construct(
        protected PaymentModeRepository $paymentModeRepository,
        protected CompanyService $companyService,
    ) {}

    public function getAll()
    {
        return $this->paymentModeRepository->all();
    }

    public function getById($id)
    {
        return $this->paymentModeRepository->find($id);
    }

    public function createOrRestore(array $data, $user_id = null)
    {
        $this->validate($data);
        $data['added_by'] = $user_id ? $user_id : auth()->user()->id;
        $data['payment_mode_code'] = $this->setPaymentModeCode();

        $existing = $this->paymentModeRepository->checkIfExist($data);

        if ($existing) {
            if ($existing->trashed()) {
                $existing->restore();
            }
            $existing->fill($data);
            $existing->save();
            return $existing;
        }

        return $this->paymentModeRepository->create($data);
    }

    public function update($id, array $data)
    {
        $this->validate($data, $id);
        $data['updated_by'] = auth()->user()->id;
        return $this->paymentModeRepository->updateOrRestore($id, $data);
    }

    public function delete($id)
    {
        return $this->paymentModeRepository->delete($id);
    }

    public function setPaymentModeCode($addval = 1)
    {
        $codeService = new \App\Services\CodeGeneratorService();
        return $codeService->generateNextCode('payment_modes', 'payment_mode_code', 'PMD', 5, $addval);
    }

    private function validate(array $data, $id = null)
    {
        $validator = Validator::make($data, [
            'payment_mode_name' => [
                'required',
                Rule::unique('payment_modes')->ignore($id)
                    ->where(fn($q) => $q
                        // ->where('company_id', $data['company_id'])
                        ->whereNull('deleted_at'))
            ],
            'payment_mode_short_code' => 'required',
            // 'company_id' => 'required|exists:companies,id',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    public function getDataTable(array $filters = [])
    {
        $query = $this->paymentModeRepository->getQuery($filters);

        $columns = [
            ['data' => 'DT_RowIndex', 'name' => 'id'],
            // ['data' => 'company_name', 'name' => 'company_name'],
            ['data' => 'payment_mode_name', 'name' => 'payment_mode_name'],
            ['data' => 'payment_mode_arabic_name', 'name' => 'payment_mode_arabic_name'],
            ['data' => 'payment_mode_short_code', 'name' => 'payment_mode_short_code'],
            ['data' => 'action', 'name' => 'action', 'orderable' => true, 'searchable' => true],
        ];

        return datatables()
            ->of($query)
            ->addIndexColumn()
            // ->addColumn('company_name', fn($row) => $row->company->company_name ?? '-')
            ->addColumn('payment_mode_name', fn($row) => $row->payment_mode_name ?? '-')
            ->addColumn('payment_mode_arabic_name', fn($row) => $row->payment_mode_arabic_name ?? '-')
            ->addColumn('payment_mode_short_code', fn($row) => $row->payment_mode_short_code ?? '-')
            ->addColumn('action', function ($row) {
                $action = '<div class="d-flex flex-column flex-md-row ">';
                if (Gate::allows('payment_mode.edit')) {
                    $action .= '<button class="btn btn-info mb-1 mr-md-1" data-toggle="modal"
                                                        data-target="#modal-payment-mode"
                                                        data-row=\'' .  json_encode($row)  . '\'>Edit</button>';
                }
                if (Gate::allows('payment_mode.delete')) {
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
        // dd("test");
        // Read Excel as collection
        $rows = Excel::toCollection(new PaymentModeImport, $file)->first();

        $insertData = [];
        $restoreCount = 0;
        // dd($rows);
        foreach ($rows as $key => $row) {
            // dd($row);
            // print_r($row);
            // $company_id = $this->companyService->getIdByCompanyname($row['company']);

            // if ($company_id == null) {
            //     $existing = $this->companyService->checkIfExist(array('company_name' => $row['company'], 'payment_mode_name' => $row['payment_mode_name']));

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

            // $paymentModeexist = $this->paymentModeRepository->checkIfExist(array('payment_mode_name' => $row['payment_mode_name'])); //, 'company_id' => $company_id
            // // dd($paymentModeexist);

            // if (empty($paymentModeexist)) {
            //     $insertData[] = [
            //         // 'company_id' => $company_id,
            //         'payment_mode_code' => $this->setPaymentModeCode($key + 1),
            //         'payment_mode_name' => $row['payment_mode_name'],
            //         'payment_mode_short_code' => $row['payment_mode_short_code'],
            //         'created_at' => now(),
            //         'updated_at' => now(),
            //         'added_by' => $user_id,
            //     ];
            //     // dd($insertData);
            // }
            $paymentModeexist = $this->paymentModeRepository->checkIfExist([
                'payment_mode_name' => $row['payment_mode_name']
            ]);

            if ($paymentModeexist) {

                // If it exists but is soft-deleted → restore it
                if ($paymentModeexist->trashed()) {
                    $paymentModeexist->restore();
                    $restoreCount++;
                }
            } else {

                // Only insert if it truly does not exist
                $insertData[] = [
                    'payment_mode_code' => $this->setPaymentModeCode($key + 1),
                    'payment_mode_name' => $row['payment_mode_name'],
                    'payment_mode_short_code' => $row['payment_mode_short_code'],
                    'created_at' => now(),
                    'updated_at' => now(),
                    'added_by' => $user_id,
                ];
            }
        }
        // dd($insertData);

        $this->paymentModeRepository->insertBulk($insertData);

        // return count($insertData);
        return [
            'inserted' => count($insertData),
            'restored' => $restoreCount,
        ];
    }
}
