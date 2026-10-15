<?php

namespace App\Services;

use App\Imports\AreaImport;
use App\Repositories\AreaRepository;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class AreaService
{
    public function __construct(
        protected AreaRepository $areaRepository,
        protected CompanyService $companyService,
    ) {}

    

    public function getAll()
    {
        return $this->areaRepository->all();
    }

    public function getById($id)
    {
        return $this->areaRepository->find($id);
    }

    public function getByName($name)
    {
        return $this->areaRepository->getByName($name);
    }

    public function createOrRestore(array $data, $user_id = null)
    {
        $this->validate($data);
        $data['added_by'] = $user_id ? $user_id : auth()->user()->id;
        $data['area_code'] = $this->setAreaCode();

        $existing = $this->areaRepository->checkIfExist($data);

        if ($existing) {
            if ($existing->trashed()) {
                $existing->restore();
            }
            $existing->fill($data);
            $existing->save();
            return $existing;
        }

        return $this->areaRepository->create($data);
    }

    public function update($id, array $data)
    {
        $this->validate($data, $id);
        $data['updated_by'] = auth()->user()->id;
        return $this->areaRepository->updateOrRestore($id, $data);
    }

    public function delete($id)
    {
        return $this->areaRepository->delete($id);
    }

    public function setAreaCode($addval = 1)
    {
        $codeService = new \App\Services\CodeGeneratorService();
        return $codeService->generateNextCode('areas', 'area_code', 'ARE', 5, $addval);
    }

    public function getByCompany($company)
    {
        return $this->areaRepository->getByCompany($company);
    }

    private function validate(array $data, $id = null)
    {
        $validator = Validator::make($data, [
            // 'company_id' => 'required|exists:companies,id',
            'area_name' => [
                'required',
                'string',
                Rule::unique('areas')->ignore($id)
                    // ->where(fn($query) => $query->where('company_id', $data['company_id']))
                    ->whereNull('deleted_at'),
            ],
        ], [
            'area_name.unique' => 'This area name already exists. Please choose another.',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    public function getDataTable(array $filters = [])
    {
        $query = $this->areaRepository->getQuery($filters);

        $columns = [
            ['data' => 'DT_RowIndex', 'name' => 'id'],
            ['data' => 'area_name', 'name' => 'area_name'],
            // ['data' => 'company_name', 'name' => 'company_name'],
            ['data' => 'action', 'name' => 'action', 'orderable' => true, 'searchable' => true],
        ];

        return datatables()
            ->of($query)
            ->addIndexColumn()
            ->addColumn('area_name', fn($row) => $row->area_name ?? '-')
            // ->addColumn('company_name', fn($row) => $row->company->company_name ?? '-')
            ->addColumn('action', function ($row) {
                $action = '<div class="d-flex flex-column flex-md-row ">';
                if (Gate::allows('area.edit')) {
                    $action .= '<button class="btn btn-info mb-1 mr-md-1" data-toggle="modal"
                                                        data-target="#modal-area" data-id="' . $row->id . '"
                                                        data-name="' . $row->area_name . '"
                                                        data-company="' . $row->company_id . '"
                                                         data-status="' . $row->status . '"
                                                        >Edit</button>';
                }
                if (Gate::allows('area.view')) {
                    $action .= '<a href="' . route('areas.show', $row->id) . '" class="btn btn-warning mb-1 mr-md-1">View</a>';
                }
                if (Gate::allows('area.delete')) {
                    $action .= '<button class="btn btn-danger mb-1" onclick="deleteConf(' . $row->id . ')" type="submit">Delete</button>';
                }
                $action .= '</div>';

                return $action ?: '-';
            })
            ->rawColumns(['action'])
            ->with(['columns' => $columns]) // send columns too
            ->toJson();
    }

    public function checkIfExist($data)
    {
        return $this->areaRepository->checkIfExist($data);
    }

    // public function exportExcel(array $filters = [])
    // {
    //     return Excel::download(new AreaExport($filters), 'areas.xlsx');
    // }

    public function importExcel($file, $user_id)
    {
        // Read Excel as collection
        $rows = Excel::toCollection(new AreaImport, $file)->first();

        $insertData = [];
        $restoreCount = 0;
        foreach ($rows as $rowindx => $row) {
            // dd($row);
            // $company_id = $this->existCheck(
            //     'companyService',
            //     'getByData',
            //     'checkIfExist',
            //     array('company_name' => $row['company_name']),
            //     ['company_name' => $row['company_name'], 'email' => 'company@demo.com', 'industry_id' => '1', 'phone' => 0000000, 'company_short_code' => $row['company_name']],
            //     $user_id
            // );
            $areaExist = $this->checkIfExist(array('area_name' => $row['area_name']));
            // dd($areaExist);
            if ($areaExist) {
                if ($areaExist->trashed()) {
                    $areaExist->restore();
                    $restoreCount++;
                }
            } else {
                $insertData[] = [
                    // 'company_id' => $company_id,
                    'area_name' => $row['area_name'],
                    'area_code' => $this->setAreacode($rowindx + 1),
                    'added_by' => $user_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
        }


        $this->areaRepository->insertBulk($insertData);

        // return count($insertData);
        return [
            'inserted' => count($insertData),
            'restored' => $restoreCount,
        ];
    }

    public function existCheck($service, $funName, $existfuncName, $data1, $createData, $user_id)
    {
        $retId = $this->$service->$funName($data1);

        if ($retId == null) {
            $existing = $this->$service->$existfuncName($data1);

            if (!empty($existing)) {
                // echo "exist";
                $existing->restore();

                $retId = $existing->id;
            } else {
                $retId = $this->$service->createOrRestore(
                    $createData,
                    $user_id
                )->id;
            }
        } else {
            $retId = $retId->id;
        }

        return $retId;
    }
}
