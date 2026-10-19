<?php

namespace App\Services;

use App\Imports\LocalityImport;
use App\Repositories\LocalityRepository;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\UnitType;
use App\Models\LocalityPricing;

class LocalityService
{
    protected $localityRepository;
    protected $companyService;
    protected $areaService;


    public function __construct(LocalityRepository $localityRepository, CompanyService $companyService, AreaService $areaService)
    {
        $this->localityRepository = $localityRepository;
        $this->companyService = $companyService;
        $this->areaService = $areaService;
    }

    public function getAll()
    {
        return $this->localityRepository->all();
    }

    public function getById($id)
    {
        return $this->localityRepository->find($id);
    }

    public function createOrRestore(array $data, $user_id = null)
    {
        $this->validate($data);
        $data['added_by'] = auth()->user()->id;
        $data['locality_code'] = $this->setLocalityCode();

        $existing = $this->localityRepository->checkIfExist($data);

        if ($existing) {
            if ($existing->trashed()) {
                $existing->restore();
            }
            $existing->fill($data);
            $existing->save();
            return $existing;
        }

        // Otherwise create fresh
        return $this->localityRepository->create($data);
    }

    public function update($id, array $data)
    {
        $this->validate($data, $id);
        $data['updated_by'] = auth()->user()->id;
        return $this->localityRepository->updateOrRestore($id, $data);
    }

    public function delete($id)
    {
        return $this->localityRepository->delete($id);
    }

    public function getByData($name)
    {
        return $this->localityRepository->getByData($name);
    }

    public function setLocalityCode($addval = 1)
    {
        $codeService = new \App\Services\CodeGeneratorService();
        return $codeService->generateNextCode('localities', 'locality_code', 'LOC', 5, $addval);
    }

    private function validate(array $data, $id = null)
    {
        $validator = Validator::make($data, [
            // 'company_id' => 'required|exists:companies,id',
            'area_id' => 'required|exists:areas,id',
            'locality_name' => [
                'required',
                'string',
                Rule::unique('localities')
                    ->ignore($id)
                    ->where(
                        fn($query) =>
                        $query->where('area_id', $data['area_id'] ?? null)
                            // ->where('company_id', $data['company_id'] ?? null)
                            ->whereNull('deleted_at'),
                    )
            ],
        ], [
            'locality_name.unique' => 'This locality name already exists. Please choose another.',
            'area_id.unique' => 'This area already exists. Please choose another.',
            // 'company_id.unique' => 'This company already exists. Please choose another.',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    
    

    public function getDataTable(array $filters = [])
    {
        $query = $this->localityRepository
            ->getQuery($filters)
            ->with(['area', 'pricings']);

        $unitTypes = UnitType::all();

        $dataTable = datatables()
            ->of($query)
            ->addIndexColumn()
            ->addColumn('locality_name', function ($row) {
                return $row->locality_name ?? '-';
            })
            ->addColumn('area_name', function ($row) {
                return $row->area->area_name ?? '-';
            });

        foreach ($unitTypes as $unitType) {
            $dataTable->addColumn('unit_type_' . $unitType->id, function ($row) use ($unitType) {
                $pricing = $row->pricings->firstWhere('unit_type_id', $unitType->id);

                if ($pricing === null || $pricing->price === null) {
                    return '-';
                }

                return number_format((float) $pricing->price, 2);
            });
        }

        $dataTable->addColumn('status', function ($row) {
            if ((int) $row->status === 1) {
                return '<span class="badge bg-success">Active</span>';
            }

            return '<span class="badge bg-secondary">Inactive</span>';
        });

        $dataTable->addColumn('action', function ($row) {
            $action = '<div class="d-flex flex-column flex-md-row">';
            if (Gate::allows('locality.location_pricing')) {
                $action .= '<button type="button"
                    class="btn btn-success mb-1 mr-md-1"
                    data-toggle="modal"
                    data-target="#modal-locality-pricing"
                    data-id="' . $row->id . '"
                    data-name="' . e($row->locality_name) . '"
                    data-area="' . e($row->area->area_name ?? '') . '">
                    Pricing
                </button>';
            }

            if (Gate::allows('locality.edit')) {
                $action .= '<button class="btn btn-info mb-1 mr-md-1"
                    data-toggle="modal"
                    data-target="#modal-locality"
                    data-id="' . $row->id . '"
                    data-name="' . e($row->locality_name) . '"
                    data-company="' . $row->company_id . '"
                    data-area="' . $row->area_id . '"
                    data-status="' . $row->status . '">
                    Edit
                </button>';
            }

            if (Gate::allows('locality.view')) {
                $action .= '<a href="' . route('locality.show', $row->id) . '"
                    class="btn btn-warning mb-1 mr-md-1">View</a>';
            }

            if (Gate::allows('locality.delete')) {
                $action .= '<button class="btn btn-danger mb-1"
                    onclick="deleteConf(' . $row->id . ')"
                    type="button">Delete</button>';
            }

            $action .= '</div>';

            return $action;
        });

        return $dataTable
            ->rawColumns(['action', 'status'])
            ->toJson();
    }


    public function importExcel($file, $user_id)
    {
        // Read Excel as collection
        $rows = Excel::toCollection(new LocalityImport, $file)->first();

        $insertData = [];
        $restoredCount = 0;
        foreach ($rows as $key => $row) {
            // dd($row);
            $area = $this->areaService->getByName(['area_name' => $row['area']]);
            // $company_id = $this->companyService->getIdByCompanyname($row['company']);

            if (empty($area)) {
                // $existing = $this->areaService->checkIfExist(array('company_id' => $company_id, 'area_name' => $row['area']));
                $existing = $this->areaService->checkIfExist(array('area_name' => $row['area']));


                if (!empty($existing)) {
                    // echo "exist";
                    $existing->restore();

                    $area = $existing;
                } else {
                    $area = $this->areaService->createOrRestore([
                        // 'company_id' => $company_id,
                        'area_name' => $row['area'],
                    ], $user_id);
                }
            }

            $localityExist = $this->checkIfExist(array('area_id' => $area->id, 'locality_name' => $row['locality']));



            if ($localityExist) {
                if ($localityExist->trashed()) {
                    $localityExist->restore();
                    $restoredCount++;
                }
            } else {

                $insertData[] = [
                    // 'company_id' => $area->company_id,
                    'area_id' => $area->id,
                    'locality_code' => $this->setLocalityCode($key + 1),
                    'locality_name' => $row['locality'],
                    'created_at' => now(),
                    'updated_at' => now(),
                    'added_by' => $user_id,
                ];
            }
        }

        $this->localityRepository->insertBulk($insertData);

        // return count($insertData);
        return [
            'inserted' => count($insertData),
            'restored' => $restoredCount,
        ];
    }

    public function checkIfExist($data)
    {
        return $this->localityRepository->checkIfExist($data);
    }
}
