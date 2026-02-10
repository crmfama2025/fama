<?php

namespace App\Services;

use App\Exports\PropertyExport;
use App\Imports\PropertyImport;
use App\Repositories\PropertyRepository;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class PropertyService
{
    public function __construct(
        protected PropertyRepository $propertyRepository,
        protected CompanyService $companyService,
        protected AreaService $areaService,
        protected LocalityService $localityService,
        protected PropertyTypeService $propertyTypeService
    ) {}

    public function getAll()
    {
        return $this->propertyRepository->all();
    }

    public function getById($id)
    {
        return $this->propertyRepository->find($id);
    }

    public function getByName($name)
    {
        return $this->propertyRepository->getByName($name);
    }

    public function createOrRestore(array $data, $user_id = null)
    {
        // dd($data);
        $this->validate($data);
        $data['added_by'] = $user_id ? $user_id : auth()->user()->id;
        $data['property_code'] = $this->setPropertyCode();

        $existing = $this->propertyRepository->checkIfExist($data);

        if ($existing) {
            if ($existing->trashed()) {
                $existing->restore();
            }
            $existing->fill($data);
            $existing->save();
            return $existing;
        }

        return $this->propertyRepository->create($data);
    }

    public function update($id, array $data)
    {
        // dd($data);
        $this->validate($data, $id);
        $data['updated_by'] = auth()->user()->id;
        return $this->propertyRepository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->propertyRepository->delete($id);
    }

    public function setPropertyCode($addval = 1)
    {
        $codeService = new \App\Services\CodeGeneratorService();
        return $codeService->generateNextCode('properties', 'property_code', 'PRP', 5, $addval);
    }

    private function validate(array $data, $id = null)
    {
        // dd($data);
        $validator = Validator::make($data, [
            // 'company_id' => 'required|exists:companies,id',
            'area_id' => 'required|exists:areas,id',
            'locality_id' => 'required|exists:localities,id',
            // 'property_type_id' => 'required|exists:property_types,id',
            'plot_no' => 'required',
            'property_name' => [
                'required',
                'string',
                Rule::unique('properties')->ignore($id)->where(function ($query) use ($data) {
                    $query->where('area_id', $data['area_id'] ?? null)
                        // ->where('company_id', $data['company_id'] ?? null)
                        ->where('locality_id', $data['locality_id'] ?? null)
                        ->where('plot_no', $data['plot_no'] ?? null)
                        ->whereNull('deleted_at');
                }),
            ],

            // Property Size and Unit
            // 'property_size' => 'required|numeric|min:0',
            // 'property_size_unit' => 'required|exists:property_size_units,id',
            'status' => 'required|in:0,1',
            // 'makani_number' => ['nullable', 'digits:10'],
            'makani_number' => ['nullable', 'regex:/^\d{5}\s\d{5}$/'],

            'location' => 'nullable|url',
        ], [
            'property_name.unique' => 'This property name already exists. Please choose another.',
            'area_id.required' => 'Please select an area.',
            'locality_id.required' => 'Please select a locality.',
            'plot_no.required' => 'Plot number is required.',
            // 'property_size.required' => 'Property size is required.',
            // 'property_size_unit.required' => 'Please select a property size unit.',
            'status.required' => 'Please select a status.',
            // 'makani_number.digits' => 'Makani Number must be exactly 10 digits and cannot contain letters or symbols.',
            // 'property_type_id.required' => 'Please select a propert type.',
            'location.url' => 'Please enter a valid URL in the Location field (starting with http:// or https://).',



        ]);
        // dd($validator);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    public function getDataTable(array $filters = [])
    {
        $query = $this->propertyRepository->getQuery($filters);

        $columns = [
            ['data' => 'DT_RowIndex', 'name' => 'id'],
            // ['data' => 'company_name', 'name' => 'company_name'],
            ['data' => 'area_name', 'name' => 'area_name'],
            ['data' => 'locality_name', 'name' => 'locality_name'],
            // ['data' => 'property_type', 'name' => 'property_type'],
            ['data' => 'property_name', 'name' => 'property_name'],
            ['data' => 'property_size_unit', 'name' => 'property_size_unit'],
            ['data' => 'property_size', 'name' => 'property_size'],
            ['data' => 'plot_no', 'name' => 'plot_no'],
            ['data' => 'makani_number', 'name' => 'makani_number'],
            ['data' => 'action', 'name' => 'action', 'orderable' => true, 'searchable' => true],
        ];

        return datatables()
            ->of($query)
            ->addIndexColumn()
            ->addColumn('property_name', fn($row) => $row->property_name ?? '-')
            // ->addColumn('company_name', fn($row) => $row->company_name ?? '-')
            ->addColumn('area_name', fn($row) => $row->area_name ?? '-')
            ->addColumn('locality_name', fn($row) => $row->locality_name ?? '-')
            // ->addColumn('property_type', fn($row) => $row->property_type ?? '-')
            ->addColumn('property_size', fn($row) => $row->property_size . ' ' . $row->unit_name ?? '-')
            ->addColumn('action', function ($row) {
                $action = '<div class="d-flex flex-column flex-md-row ">';
                if (Gate::allows('property.edit')) {
                    $action .= '<button class="btn btn-info mb-1 mr-md-1" data-toggle="modal"
                                                        data-target="#modal-property" data-id="' . $row->id . '"
                                                        data-name="' . $row->property_name . '"
                                                        data-company="' . $row->company_id . '"
                                                        data-area="' . $row->area_id . '"
                                                        data-locality="' . $row->locality_id . '"

                                                        data-property_size="' . $row->property_size . '"
                                                        data-property_size_unit="' . $row->property_size_unit . '"
                                                        data-plot_no="' . $row->plot_no . '"
                                                        data-lat="' . $row->latitude . '"
                                                        data-long="' . $row->longitude . '"
                                                        data-address="' . $row->address . '"
                                                        data-remarks="' . $row->remarks . '"
                                                        data-location="' . $row->location . '"
                                                        data-status="' . $row->status . '"
                                                        data-makani="' . $row->makani_number . '"

                                                        >Edit</button>';  //data-property_type="' . $row->property_type_id . '"
                }
                if (Gate::allows('property.view')) {
                    $action .= '<a href="' . route('property.show', $row->id) . '" class="btn btn-warning mb-1 mr-md-1">View</a>';
                }
                if (Gate::allows('property.delete')) {
                    $action .= '<button class="btn btn-danger mb-1" onclick="deleteConf(' . $row->id . ')" type="submit">Delete</button>';
                }
                $action .= '</div>';

                return $action ?: '-';
            })
            ->rawColumns(['action'])
            ->with(['columns' => $columns]) // send columns too
            ->toJson();
    }

    // public function checkIfExist($data)
    // {
    //     return $this->propertyRepository->checkIfExist($data);
    // }

    public function exportExcel(array $filters = [])
    {
        return Excel::download(new PropertyExport($filters), 'properties.xlsx');
    }

    public function importExcel($file, $user_id)
    {
        // Read Excel as collection
        $rows = Excel::toCollection(new PropertyImport, $file)->first();

        $insertData = [];
        $seen = [];
        $restoreCount = 0;
        foreach ($rows as $rowindx => $row) {
            // print_r($row);


            // $company_id = $this->existCheck(
            //     'companyService',
            //     'getByData',
            //     'checkIfExist',
            //     array('company_name' => $row['company_name']),
            //     ['company_name' => $row['company_name'], 'email' => 'company@demo.com', 'industry_id' => '1', 'phone' => 0000000, 'company_short_code' => $row['company_name']],
            //     $user_id
            // );

            // $area_id = $this->existCheck(
            //     'areaService',
            //     'getByName',
            //     'checkIfExist',
            //     array('company_id' => $company_id, 'area_name' => $row['area']),
            //     [
            //         'company_id' => $company_id,
            //         'area_name' => $row['area']
            //     ],
            //     $user_id
            // );
            //  $locality_id = $this->existCheck(
            //     'localityService',
            //     'getByData',
            //     'checkIfExist',
            //     array('company_id' => $company_id, 'area_id' => $area_id, 'locality_name' => $row['location']),
            //     [
            //         'company_id' => $company_id,
            //         'area_id' => $area_id,
            //         'locality_name' => $row['location']
            //     ],
            //     $user_id
            // );
            $area_id = $this->existCheck(
                'areaService',
                'getByName',
                'checkIfExist',
                array('area_name' => $row['area']),
                [
                    'area_name' => $row['area']
                ],
                $user_id
            );

            $locality_id = $this->existCheck(
                'localityService',
                'getByData',
                'checkIfExist',
                array('area_id' => $area_id, 'locality_name' => $row['location']),
                [
                    'area_id' => $area_id,
                    'locality_name' => $row['location']
                ],
                $user_id
            );

            // $property_type_id = $this->existCheck(
            //     'propertyTypeService',
            //     'getByData',
            //     'checkIfExist',
            //     array('company_id' => $company_id, 'property_type' => $row['property_type']),
            //     [
            //         'company_id' => $company_id,
            //         'property_type' => $row['property_type']
            //     ],
            //     $user_id
            // );


            // $seenKey = $company_id . '-' . $area_id . '-' . $locality_id . '-' . strtolower($row['building_name']);
            $seenKey = $area_id . '-' . $locality_id . '-' . $row['plot_number'] . '-' . strtolower($row['building_name']);

            $propertyExist = $this->propertyRepository->checkIfExist(array(
                // 'company_id' => $company_id,
                'area_id' => $area_id,
                'locality_id' => $locality_id,
                'property_name' => $row['building_name'],
                'plot_no' => $row['plot_number'],
            ));
            // dd($propertyExist);
            if ($propertyExist) {
                if ($propertyExist->trashed()) {
                    $propertyExist->restore();
                    $restoreCount++;
                }
            } elseif ($propertyExist == null) {
                if (!isset($seen[$seenKey])) {
                    $insertData[] = [
                        // 'company_id' => $company_id,
                        'area_id' => $area_id,
                        'locality_id' => $locality_id,
                        // 'property_type_id' => $property_type_id,
                        'property_code' => $this->setPropertyCode($rowindx + 1),
                        'property_name' => $row['building_name'],
                        'property_size' => $row['property_size'],
                        'property_size_unit' => $row['property_size_unit'],
                        'plot_no' => $row['plot_number'],
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'added_by' => $user_id,
                    ];
                    $seen[$seenKey] = true;
                }
            }


            // if ($propertyExist == null) {

            //     if (!isset($seen[$seenKey])) {
            //         $insertData[] = [
            //             // 'company_id' => $company_id,
            //             'area_id' => $area_id,
            //             'locality_id' => $locality_id,
            //             // 'property_type_id' => $property_type_id,
            //             'property_code' => $this->setPropertyCode($rowindx + 1),
            //             'property_name' => $row['building_name'],
            //             'property_size' => $row['property_size'],
            //             'property_size_unit' => $row['property_size_unit'],
            //             'plot_no' => $row['plot_number'],
            //             'created_at' => now()->toDateTimeString(),
            //             'updated_at' => now()->toDateTimeString(),
            //             'added_by' => $user_id,
            //         ];
            //         $seen[$seenKey] = true;
            //     }
            // }
        }

        $this->propertyRepository->insertBulk($insertData);

        // return count($insertData);
        return ['inserted' => count($insertData), 'restored' => $restoreCount];
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
