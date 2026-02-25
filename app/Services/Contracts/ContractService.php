<?php

namespace App\Services\Contracts;

use App\Models\Contract;
use App\Models\ContractUnitDetail;
use App\Repositories\Contracts\ContractRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Models\{
    Installment,
    PaymentMode,
    Bank,
    ContractType,
    UnitType,
    UnitStatus,
    UnitSizeUnit
};
use App\Services\AreaService;
use App\Services\CompanyService;
use App\Services\InstallmentService;
use App\Services\LocalityService;
use App\Services\PayableClearingService;
use App\Services\PropertyService;
use App\Services\PropertyTypeService;
use App\Services\VendorService;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class ContractService
{
    public function __construct(
        protected ContractRepository $contractRepo,
        protected ContractDetailService $detailServ,
        protected OtcService $otcServ,
        protected PaymentService $paymentServ,
        protected PaymentDetailService $paymentdetServ,
        protected UnitService $unitServ,
        protected UnitDetailService $unitDetServ,
        protected RentalService $rentalServ,

        protected CompanyService $companyService,
        protected LocalityService $localityService,
        protected AreaService $areaService,
        protected PropertyTypeService $propertyTypeServ,
        protected PropertyService $propertyServ,
        protected InstallmentService $installmentServ,
        protected VendorService $vendorServ,

        protected VendorContractSign $vendorSignServ,
        protected PayableClearingService $payableServ,
    ) {}

    public function getAll()
    {
        return $this->contractRepo->all();
    }

    public function getById($id)
    {
        return $this->contractRepo->find($id);
    }

    public function getAllDataById($id)
    {
        return $this->contractRepo->getAllDataById($id);
    }

    public function getindirect($id)
    {
        return $this->contractRepo->getindirect($id);
    }

    public function getDropdownData($subModule): array
    {
        $companies = $this->companyService->getAll('contract', $subModule);
        $companyIds = $companies->pluck('id');

        return [
            'companies' => $companies,
            'localities' => $this->localityService->getAll(),
            'areas' => $this->areaService->getAll(),
            'property_types' => $this->propertyTypeServ->getAll(),
            'properties' => $this->propertyServ->getAll(),
            'installments' => $this->installmentServ->getAll(),
            'vendors' => $this->vendorServ->getAll(),
            'installments' => Installment::all(),
            'paymentmodes' => PaymentMode::all(),
            'banks' => Bank::withoutGlobalScopes()->where('status', 1)
                ->whereIn('company_id', $companyIds)
                ->get(),
            'contractTypes' => ContractType::all(),
            'UnitTypes' => UnitType::all(),
            'UnitStatus' => UnitStatus::all(),
            'UnitSizeUnit' => UnitSizeUnit::all(),
            'indirect' => $this->contractRepo->allNotIndirect(),
        ];
    }


    public function createOrRestore(array $data, $user_id = null)
    {
        // dd($data);
        $data['contract']['added_by'] = $user_id ? $user_id : auth()->user()->id;
        $data['contract']['project_code'] = $this->setProjectCode();

        $contract_renewal_status = 0;
        if (isset($data['contract']['renewal'])) {
            $data['contract']['renewal_date'] = Carbon::today()->format('Y-m-d');
            $data['contract']['renewed_by'] = $user_id ? $user_id : auth()->user()->id;
            $data['contract']['parent_contract_id'] = $data['contract']['id'];


            $contract_renewal_status = 1;
        }

        return DB::transaction(function () use ($data, $contract_renewal_status) {
            $this->validate($data['contract'], (!isset($data['contract']['renewal'])) ? $data['contract']['id'] ?? null : 0);
            $indirectContractId = $data['contract']['indirect_contract_id'] ?? null;

            if (!empty($indirectContractId)) {
                $data['contract']['indirect_status'] = 1;
            } else {
                $data['contract']['indirect_status'] = 0;
                $data['contract']['indirect_contract_id'] = 0;
                $data['contract']['indirect_company_id'] = 0;
            }

            $contract = $this->contractRepo->create($data['contract']);
            // dd($contract->indirect_status);
            if ($contract->indirect_status == 1 && $contract->indirect_contract_id != 0) {
                // dd("test");
                $this->contractRepo->update($contract->indirect_contract_id, [
                    'is_indirect_contract' => 1
                ]);
            }
            // Store related details

            if ($contract_renewal_status == 1) {
                $this->contractRepo->update($data['contract']['id'], array(
                    'contract_renewal_status' => $contract_renewal_status,
                ));
            }

            $this->detailServ->create($contract->id, $data['detail'] ?? []);

            $unitData = $this->unitServ->create($contract->id, $data['unit'] ?? [], $data['unit_detail'] ?? []);
            // dd($unitData);
            $this->unitDetServ->create($contract, $data['unit_detail'] ?? [], $data['rentals']['receivable_installments'], $unitData->id);
            // dd($contract);
            $this->rentalServ->create($contract->id, $data['rentals'] ?? []);
            $this->otcServ->create($contract->id, $data['otc'] ?? []);

            $this->paymentServ->create($contract->id, $data['payment'] ?? [], $data['payment_detail'] ?? [], $data['receivables'] ?? []);


            return $contract;
        });
        // $existing = $this->contractRepo->checkIfExist($data);

        // if ($existing) {
        //     if ($existing->trashed()) {
        //         $existing->restore();
        //     }
        //     $existing->fill($data);
        //     $existing->save();
        //     return $existing;
        // }

        // return $this->contractRepo->create($data);
    }

    public function setProjectCode($addval = 1)
    {
        $codeService = new \App\Services\CodeGeneratorService();
        return $codeService->generateNextCode('contracts', 'project_code', 'PRJ', 5, $addval);
    }

    public function update($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $data['contract']['updated_by'] = auth()->user()->id;
            $this->validate($data['contract'], $data['contract']['id'] ?? null);
            // dd($data['contract']);
            $indirectContractId = $data['contract']['indirect_contract_id'] ?? null;

            if (!empty($indirectContractId)) {
                $data['contract']['indirect_status'] = 1;
            } else {
                $data['contract']['indirect_status'] = 0;
                $data['contract']['indirect_contract_id'] = 0;
                $data['contract']['indirect_company_id'] = 0;
            }

            $contract = $this->contractRepo->update($id, $data['contract']);
            $ind_ct_id = $contract->indirect_contract_id;
            // dd($ind_ct_id);
            // dd($contract);
            // Store related details
            if (
                $contract->indirect_status == 1 &&
                $contract->indirect_contract_id !== 0
            ) {
                $this->contractRepo->update(
                    $ind_ct_id,
                    [
                        'is_indirect_contract' => 1,
                    ]
                );
            }


            $this->detailServ->update($data['detail'] ?? []);
            $unitData = $this->unitServ->update($data['unit'] ?? [], $data['unit_detail'] ?? []);
            // dd($unitData);
            $this->unitDetServ->update($contract, $data['unit_detail'] ?? [], $data['rentals']['receivable_installments'], $unitData->id);
            // dd($unitData);
            $this->rentalServ->update($data['rentals'] ?? []);
            $this->otcServ->update($data['otc'] ?? []);

            $this->paymentServ->update($id, $data['payment'] ?? [], $data['payment_detail'] ?? [], $data['receivables'] ?? []);


            return $contract;
        });

        // return $this->contractRepo->update($id, $data);
    }

    private function validate(array $data, $id = null)
    {
        $validator = Validator::make($data, [
            'project_number' => [
                'required',
                'string',
                Rule::unique('contracts')->ignore($id)
                    ->where(fn($query) => $query->where('company_id', $data['company_id']))
                    ->whereNull('deleted_at'),
            ],
            'company_id' => 'required',
            'vendor_id' => 'required',
            'contract_type_id' => 'required',
            'area_id' => 'required',
            'locality_id' => 'required',
            'property_id' => 'required',
        ], [
            'project_number.unique' => 'This project number already exists. Please choose another.',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    // public function checkIfExist($data)
    // {
    //     return $this->contractRepo->checkIfExist($data);
    // }

    public function delete($id)
    {
        return $this->contractRepo->delete($id);
    }

    public function getDataTable(array $filters = [])
    {
        $query = $this->contractRepo->getQuery($filters);
        // dd($query);

        $columns = [
            ['data' => 'DT_RowIndex', 'name' => 'id'],
            ['data' => 'project_number', 'name' => 'project_number'],
            ['data' => 'indirect_project', 'name' => 'project_number'],
            ['data' => 'business_type', 'name' => 'business_type'],
            ['data' => 'company_name', 'name' => 'company_name'],
            ['data' => 'no_of_units', 'name' => 'no_of_units'],
            ['data' => 'roi_perc', 'name' => 'roi_perc'],
            ['data' => 'expected_profit', 'name' => 'expected_profit'],
            ['data' => 'start_date', 'name' => 'start_date'],
            ['data' => 'end_date', 'name' => 'end_date'],
            ['data' => 'contract_status', 'name' => 'contract_status'],
            ['data' => 'action', 'name' => 'action', 'orderable' => true, 'searchable' => true],
        ];

        return datatables()
            ->of($query)
            ->addIndexColumn()
            ->addColumn('project_number', function ($row) {

                $number = 'P - ' . $row->project_number ?? '-';
                $type = $row->contract_type->contract_type ?? '-';
                // Indirect badge ONLY if indirect_contract_id != 0
                $indirectHtml = '';
                if ((int) $row->indirect_contract_id !== 0) {
                    $indirectHtml = "

                <span class='badge badge-danger' title='Indirect'>
                Indirect
                </span>
           ";
                }

                // return "<strong class=''>{$number}</strong><p class='mb-0'><span>{$type}</span></p>
                // </p>";
                $badgeClass = '';

                if ($row->contract_type_id == 1) {
                    $badgeClass = 'badge badge-df';
                } elseif ($row->contract_type_id == 2) {
                    $badgeClass = 'badge badge-ff';
                } else {
                    $badgeClass = 'badge badge-secondary';
                }

                return "<strong>{$number}</strong>
            <p class='mb-0'>
                <span class='{$badgeClass}'>{$type}</span>
            </p>
             {$indirectHtml}";
            })
            ->addColumn('indirect_project', function ($row) {
                $number = $row->indirectContract?->project_number ? 'P - ' . $row->indirectContract->project_number : '-';
                $company = $row->indirectCompany?->company_name ?? '-';

                return "<strong>{$number}</strong><br><p>{$company}</p>";
            })
            // ->addColumn('project_number', fn($row) => 'P - ' . ucfirst($row->project_number) ?? '-')
            ->addColumn('company_name', fn($row) => $row->company->company_name ?? '-')
            ->addColumn('vendor_name', fn($row) => $row->vendor->vendor_name ?? '-')
            ->addColumn('property_name', fn($row) => $row->property->property_name ?? '-')
            ->addColumn('locality_name', fn($row) => $row->locality->locality_name ?? '-')
            ->addColumn('area_name', fn($row) => $row->area->area_name ?? '-')
            ->addColumn('business_type', function ($row) {
                if ($row->business_type == 1) {
                    $type = "B2B";
                } elseif ($row->business_type == 2) {
                    $type = "B2C";
                } else {
                    $type = "-";
                }
                return "<strong class='text-uppercase'>{$type}</strong>";
            })
            ->addColumn('no_of_units', fn($row) => $row->contract_unit->no_of_units ?? '-')
            ->addColumn('roi_perc', fn($row) => $row->contract_rentals->roi_perc . '%' ?? '-')
            ->addColumn('expected_profit', fn($row) => $row->contract_rentals->expected_profit ?? '-')
            ->addColumn('start_date', fn($row) => $row->contract_detail->start_date ?? '-')
            ->addColumn('end_date', fn($row) => $row->contract_detail->end_date ?? '-')
            ->addColumn(
                'status',
                function ($row) {

                    $comment = '';
                    if ($row->contract_status == 5) {
                        $comment = '<i class="far fa-comments loadComments" data-id="' . $row->id . '"></i>'; //data-toggle="modal" data-target="#modal-hold-comment"
                    }
                    return '<span class="' . contractStatusClass($row->contract_status) . '">' . contractStatusName($row->contract_status) . '</span> ' . $comment ?? '-';
                }
            )
            ->addColumn('action', function ($row) {
                $action = '';

                if (auth()->user()->hasAnyPermission(['contract.view'], $row->company_id)) {
                    $action .= '<a class="btn btn-primary btn-sm" href="' . route('contract.show', $row->id) . '" title="View Contract">
                            <i class="fas fa-eye"></i>
                        </a> ';
                }

                if (auth()->user()->hasAnyPermission(['contract.edit'], $row->company_id) && $row->has_agreement == 0 && !in_array($row->contract_status, [3, 9, 10])) {
                    $action .= '<a class="btn btn-info btn-sm" href="' . route('contract.edit', $row->id) . '" title="Edit Contract">
                            <i class="fas fa-pencil-alt"></i>
                        </a> ';
                }

                if ($row->contract_status == 0) {

                    // if (Gate::allows('contract.view')) {
                    //     $action .= '<a class="btn btn-primary btn-sm" href="' . route('contract.show', $row->id) . '" title="View Contract">
                    //         <i class="fas fa-eye"></i>
                    //     </a> ';
                    // }

                    if (auth()->user()->hasAnyPermission(['contract.delete'], $row->company_id)) {
                        $action .= '<button class="btn btn-danger btn-sm" onclick="deleteConf(' . $row->id . ')" title="Delete Contract">
                            <i class="fas fa-trash"></i>
                        </button>';
                    }
                }
                // elseif ($row->contract_status == 1) {

                // if (Gate::allows('contract.view')) {
                //     $action .= '<a class="btn btn-primary btn-sm" href="' . route('contract.show', $row->id) . '" title="View Contract">
                //         <i class="fas fa-eye"></i>
                //     </a> ';
                // }

                // if (Gate::allows('contract.document_upload')) {
                //     $action .= '<a class="btn btn-success btn-sm" href="' . route('contract.approve', $row->id) . '" title="Send for Approval">
                //         <i class="fas fa-paper-plane"></i>
                //     </a>';
                // }
                // }
                elseif ($row->contract_status == 3) {

                    // if (Gate::allows('contract.view')) {
                    //     $action .= '<a class="btn btn-primary btn-sm" href="' . route('contract.show', $row->id) . '" title="View Contract">
                    //         <i class="fas fa-eye"></i>
                    //     </a>';
                    // }
                }

                if ($row->contract_status >= 1 && $row->contract_status != 3) {
                    if (auth()->user()->hasAnyPermission(['contract.document_upload'], $row->company_id)) {
                        $action .= '<a href="' . route('contract.documents', $row->id) . '" class="btn btn-warning btn-sm" title="Upload Documents">
                            <i class="fas fa-file"></i>
                        </a> ';
                    }


                    if (auth()->user()->hasAnyPermission(['contract.send_for_approval'], $row->company_id) && in_array($row->contract_status,  [1, 5])) {
                        $action .= '<button class="btn btn-success btn-sm" data-toggle="modal" data-id="' . $row->id . '"
                                        data-target="#modal-send-approval" title="Send for Approval">
                            <i class="fas fa-paper-plane"></i>
                        </button>';
                    }
                }

                if (auth()->user()->hasAnyPermission(['contract.approve'], $row->company_id) && $row->contract_status == 4) {
                    $action .= '<a class="btn btn-info btn-sm" href="' . route('contract.approve', $row->id) . '" title="Approve Contract">
                            <i class="fas fa-thumbs-up"></i>
                        </a>';
                }

                if (auth()->user()->hasAnyPermission(['contract.sign_after_approval'], $row->company_id) && $row->contract_status == 2) {
                    $action .= '<a class="btn btn-info btn-sm"  title="Sign Vendor Contract" data-toggle="modal" data-id="' . $row->id . '"
                                        data-target="#modal-upload">
                            <i class="fas fa-signature"></i>
                        </a>';   //href="' . route('sign.contract', $row->id) . '"
                }

                if ($row->contract_status == 7) {
                    $action .= '<a class="btn btn-danger btn-sm"  title="Terminate" data-toggle="modal" data-id="' . $row->id . '"
                                        data-target="#modal-terminate-contract">
                            <i class="fas fa-window-close"></i>
                        </a>';
                }

                return $action ?: '-';
            })

            ->rawColumns(['project_number', 'action', 'status', 'business_type', 'indirect_project'])
            ->with(['columns' => $columns])
            ->toJson();
    }

    public function getAllwithUnits()
    {
        return $this->contractRepo->allwithUnits();
    }

    public function updateAgreementStatus($id)
    {
        $units = ContractUnitDetail::where('contract_id', $id)
            ->with('contractSubUnitDetails')
            ->get();
        Contract::where('id', $id)
            ->update(['has_agreement' =>  1]);

        $allVacant = $units->every(function ($unit) {
            $unitVacant = $unit->is_vacant == 1;

            $subUnitsVacant = $unit->contractSubUnitDetails->every(function ($sub) {
                return $sub->is_vacant == 1;
            });

            return $unitVacant && $subUnitsVacant;
        });

        if ($allVacant) {
            return Contract::where('id', $id)->update(['is_agreement_added' => 1]);
        }

        return false;
    }

    public function fullContracts()
    {
        return $this->contractRepo->fullContracts();
    }
    public function getRenewalDataTable(array $filters = [])
    {
        $query = $this->contractRepo->getRenewalQuery($filters);
        // dd($query);

        $columns = [
            ['data' => 'DT_RowIndex', 'name' => 'id'],
            ['data' => 'project_number', 'name' => 'project_number'],
            ['data' => 'contract_type', 'name' => 'contract_type'],
            ['data' => 'company_name', 'name' => 'company_name'],
            ['data' => 'no_of_units', 'name' => 'no_of_units'],
            ['data' => 'roi_perc', 'name' => 'roi_perc'],
            ['data' => 'expected_profit', 'name' => 'expected_profit'],
            ['data' => 'end_date', 'name' => 'end_date'],
            ['data' => 'contract_status', 'name' => 'contract_status'],
            ['data' => 'action', 'name' => 'action', 'orderable' => true, 'searchable' => true],
        ];

        return datatables()
            ->of($query)
            ->addIndexColumn()
            ->addColumn('project_number', fn($row) => 'P - ' . ucfirst($row->project_number) ?? '-')
            ->addColumn('company_name', fn($row) => $row->company->company_name ?? '-')
            ->addColumn('contract_type', fn($row) => $row->contract_type ?? '-')
            ->addColumn('no_of_units', fn($row) => $row->contract_unit->no_of_units ?? '-')
            ->addColumn('roi_perc', fn($row) => $row->contract_rentals->roi_perc ?? '-')
            ->addColumn('expected_profit', fn($row) => $row->contract_rentals->expected_profit ?? '-')
            ->addColumn('end_date', fn($row) => $row->contract_detail->end_date ?? '-')
            // ->addColumn('status', fn($row) => $row->contract_status ?? '-')

            ->addColumn('action', function ($row) {
                $action = '';

                if (auth()->user()->hasAnyPermission(['contract.renew'], $row->company_id)) {
                    $action .= '<a class="btn btn-primary btn-sm" href="' . route('contract.renew', $row->id) . '" title="Renew COntract">
                            <i class="fas fa-sync-alt"></i>
                        </a> ';

                    $action .= '<a class="btn btn-danger btn-sm openRejectModalBtn" href="#" data-url="' . route('contract.reject_renew', $row->id) . '" data-id="' . $row->id . '" title="Reject Renewal">
                            <i class="fas fa-times"></i>
                        </a> ';
                }

                return $action ?: '-';
            })

            ->rawColumns(['action'])
            ->with(['columns' => $columns])
            ->toJson();
    }

    public function getRenewalDataCount(array $filters = [])
    {
        // $query = ${$this->contractRepo->getRenewalQuery($filters)}->count();

        $query = $this->contractRepo->getRenewalQuery($filters);

        return $query->count();
    }


    public function rejectRenew($data, $contract_id)
    {
        $dataArr = [
            'renew_reject_reason' => $data->renew_reject_reason,
            'renew_reject_status' => 1,
            'renew_rejected_by'   => auth()->user()->id,
            'contract_status' => 10 // project dropped
        ];

        return $this->contractRepo->updateRejectRenew($contract_id, $dataArr);
    }

    public function getAllChildren($contract_id)
    {
        return $this->contractRepo->getAllRelatedContracts($contract_id);
    }

    public function approveContract($data)
    {
        $dataArr = [
            'contract_status' => $data['status'],
            'contract_id' => $data['contract_id'],
        ];

        if ($data['status'] == '2') {
            $dataArr['approved_by'] = auth()->user()->id;
            $dataArr['approved_date'] = Carbon::now();;
        } else {
            $dataArr['rejected_reason'] = $data['reason'];
            $dataArr['rejected_date'] = Carbon::now();
            $dataArr['contract_rejected_by'] = auth()->user()->id;
        }
        // dd($dataArr);

        // dd($this->vendorSignServ->addImageToPdf($data['contract_id']));
        return $this->contractRepo->update($data['contract_id'], $dataArr);
    }

    // public function vendorContractSign($contract_id)
    // {
    //     $contract = $this->contractRepo->find($contract_id);
    //     $vendor = $contract->vendor;

    //     // Send email to vendor for contract signing
    //     // Implement email sending logic here
    // }


    public function terminateContract(array $data)
    {
        $this->terminate_validate($data);
        // dd($data);
        DB::transaction(function () use ($data) {

            $this->contractRepo->terminate($data);

            $this->paymentdetServ->terminatePendingPayments(
                $data['contract_id'],
                $data['terminated_date'],
                $data['balance_amount']
            );

            $this->payableServ->terminateContractPayables(
                $data
            );

            // logger('Contract termination completed');
        });
    }


    private function terminate_validate(array $data, $id = null)
    {
        $validator = Validator::make($data, [
            'contract_id'     => 'required|integer',
            'terminated_date'  => 'required|date',
            'terminated_reason'          => 'required|string',
            'balance_amount'  => 'nullable|numeric',
            'received'        => 'nullable|boolean'
        ]);


        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
    public function updateIndirectContract($contract)
    {
        $ind_id = $contract->indirect_contract_id;
        $this->contractRepo->update($ind_id, [
            'is_indirect_contract' => 0
        ]);
    }
    public function updateIndirectParent($contract)
    {
        $id = $contract->id;
        $parent_contract = Contract::where('indirect_contract_id', $id)->first();
        // dd($contract);
        $update = [
            'indirect_contract_id' =>  0,
            'indirect_company_id' =>  0,
            'indirect_status' =>  0,
        ];
        $this->contractRepo->update($parent_contract->id, $update);
    }
}
