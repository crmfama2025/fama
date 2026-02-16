<?php

namespace App\Repositories\Agreement;

use App\Models\Agreement;
use App\Models\AgreementPaymentDetail;
use App\Models\agreementSubunitRentBifurcation;
use App\Models\AgreementUnit;
use App\Models\Contract;
use App\Models\ContractSubunitDetail;
use App\Models\ContractUnitDetail;
use App\Models\TenantInvoice;
use App\Repositories\Contracts\ContractRepository;
use App\Services\Agreement\AgreementPaymentDetailService;
use App\Services\Agreement\AgreementPaymentService;
use App\Services\Agreement\AgreementTenantService;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AgreementRepository
{
    public function __construct(
        protected ContractRepository $contractRepository,
        protected AgreementPaymentDetailRepository $agreementPaymentDetailRepository


    ) {}
    public function all()
    {
        return Agreement::all();
    }

    public function find($id)
    {

        return Agreement::with(
            'contract',
            'contract.contract_type',
            'contract.contract_unit',
            'company',
            'tenant.nationality',
            'agreement_payment.agreementPaymentDetails',
            'agreement_documents',
            'agreement_units.contractSubunitDetail',
            'agreement_units.contractUnitDetail'


        )->findOrFail($id);
    }

    public function findId($data)
    {
        return Contract::where($data)->first();
    }

    public function create(array $data)
    {
        // dd($data);
        return Agreement::create($data);
    }

    public function update($id, array $data)
    {
        $agreement = $this->find($id);
        $agreement->update($data);
        return $agreement;
    }
    public function delete($id)
    {
        return DB::transaction(function () use ($id) {
            $agreement = $this->find($id);
            $agreement->deleted_by = auth()->user()->id;
            $agreement->save();
            $contract_id = $agreement->contract_id;
            $this->makeVacant($id, $contract_id);
            $agreement->delete();
            return true;
        });
    }



    public function getQuery(array $filters = []): Builder
    {
        // dd("test");
        $query = Agreement::query()
            ->select([
                'agreements.*',
                'contracts.project_number',
                'companies.company_name',
                'agreement_tenants.tenant_name',
                'agreement_tenants.tenant_email',
                'agreement_tenants.tenant_mobile',
                'contract_types.contract_type',
                // \DB::raw('SUM(agreement_payment_details.paid_amount) as paid_amount')
                'contract_units.business_type as business_type'
            ])

            ->join('contracts', 'contracts.id', '=', 'agreements.contract_id')
            ->join('properties', 'properties.id', '=', 'contracts.property_id')
            // ->join('vendors', 'vendors.id', '=', 'contracts.vendor_id')
            ->join('companies', 'companies.id', '=', 'agreements.company_id')
            ->join('agreement_tenants', 'agreement_tenants.agreement_id', '=', 'agreements.id')
            ->join('contract_types', 'contract_types.id', '=', 'contracts.contract_type_id')
            ->leftJoin('contract_units', 'contract_units.contract_id', '=', 'contracts.id');


        // $get = $query->get();
        // dd($get);

        if (!empty($filters['search'])) {
            $query->orwhere('agreement_code', 'like', '%' . $filters['search'] . '%')
                ->orWhere('project_number', 'like', '%' . $filters['search'] . '%')

                ->orWhereHas('company', function ($q) use ($filters) {
                    $q->where('company_name', 'like', '%' . $filters['search'] . '%');
                })
                ->orWhereHas('contract.contract_type', function ($q) use ($filters) {
                    $q->where('contract_type', 'like', '%' . $filters['search'] . '%');
                })
                ->orWhereHas('contract.property', function ($q) use ($filters) {
                    $q->where('property_name', 'like', '%' . $filters['search'] . '%');
                })
                ->orWhereHas('contract.contract_unit_details', function ($q) use ($filters) {
                    $q->where('unit_number', 'like', '%' . $filters['search'] . '%');
                })
                ->orWhereHas('contract.contract_subunit_details', function ($q) use ($filters) {
                    $q->where('subunit_no', 'like', '%' . $filters['search'] . '%');
                })
                ->orWhereHas('contract.contract_unit', function ($q) use ($filters) {
                    $q->whereRaw("
                    CASE
                        WHEN business_type = 1 THEN 'B2B'
                        WHEN business_type = 2 THEN 'B2C'
                    END LIKE ?
                ", ['%' . $filters['search'] . '%']);
                })
                ->orWhereHas('tenant', function ($q) use ($filters) {
                    $q->where('tenant_name', 'like', '%' . $filters['search'] . '%')
                        ->orWhere('tenant_email', 'like', '%' . $filters['search'] . '%')
                        ->orWhere('tenant_mobile', 'like', '%' . $filters['search'] . '%');
                })
                // ->orWhereHas('contract_type', function ($q) use ($filters) {
                //     $q->where('contract_type', 'like', '%' . $filters['search'] . '%');
                // })
                // 🔥 BUSINESS TYPE FIX
                ->orWhereRaw("
                    CASE
                        WHEN agreement_status = 0 THEN 'Active'
                        WHEN agreement_status = 1 THEN 'Terminated'
                        WHEN agreement_status = 2 THEN 'Expired'

                    END LIKE ?
                ", ['%' . $filters['search'] . '%'])

                ->orWhereRaw("CAST(agreements.id AS CHAR) LIKE ?", ['%' . $filters['search'] . '%']);
            $search = strtolower($filters['search']);
            if ($search === 'b2b') $query->orWhere('contract_units.business_type', 1);
            if ($search === 'b2c') $query->orWhere('contract_units.business_type', 2);
        }





        if (isset($filters['status']) && $filters['status'] !== 'all') {
            $query->where('agreements.agreement_status', $filters['status']);
        }


        // if (!empty($filters['company_id'])) {
        //     $query->Where('contracts.company_id', $filters['company_id']);
        // }

        $query->orderBy('agreements.id', 'desc');

        return $query;
    }
    public function findunits($id)
    {
        return Agreement::find($id)
            ->agreement_units()
            ->pluck('id')
            ->toArray();
    }
    public function getDetails($id)
    {

        return Agreement::with([
            'contract' => function ($query) {
                $query->with(['vendor', 'area', 'property', 'locality', 'contract_rentals', 'contract_type']);
            },
            'company',
            'tenant.nationality',
            'agreement_payment_details',
            'agreement_payment.agreementPaymentDetails.invoice',
            'agreement_payment.agreementPaymentDetails.receivedPayments',
            'agreement_payment.installment',
            'agreement_documents',
            'agreement_units.contractSubunitDetail',
            'agreement_units.contractUnitDetail.unit_type',
            'agreement_units.contractUnitDetail.contractSubUnitDetails',
            'agreement_units.agreementSubunitRentBifurcation'
        ])->findOrFail($id);
    }


    public function makeVacant($agreementid, $contractid)
    {
        DB::transaction(function () use ($agreementid, $contractid) {
            $contract = $this->contractRepository->find($contractid);
            $type = $contract->contract_type_id;
            $business_type = $contract->contract_unit->business_type;
            $agreement = $this->find($agreementid);
            $contract_unit_details = $agreement->agreement_units->pluck('contract_unit_details_id');
            // dd($contract_unit_details);
            $contract_subunits = $agreement->agreement_units
                ->pluck('subunit_ids')
                ->flatten()
                ->unique()
                ->values()
                ->toArray();


            if ($type == 2) {
                // dd('test');
                $contract_units = $contract->contract_unit_details;
                foreach ($contract_units as $unit) {
                    makeUnitVacant($unit->id, $contractid);
                }
            } else {
                // dd($contract_unit_details);
                foreach ($contract_unit_details as $unit) {
                    $unitdetail = ContractUnitDetail::find($unit);
                    if ($unitdetail) {
                        // dd($unitdetail);
                        $unitdetail->is_vacant = 0;
                        $unitdetail->save();
                    }
                }
                foreach ($contract_subunits as $contract_subunit_id) {
                    $subunitdetail = ContractSubunitDetail::find($contract_subunit_id);
                    if ($subunitdetail) {
                        $subunitdetail->is_vacant = 0;
                        $subunitdetail->save();
                    }
                }
                foreach ($contract_unit_details as $unit) {
                    $countdetails = getOccupiedDetails($unit);
                    // dd($countdetails);
                    $occupied = $countdetails['occupied'];
                    $vacant = $countdetails['vacant'];
                    $unitdetail = ContractUnitDetail::find($unit);
                    if ($unitdetail) {
                        // dd($unitdetail);
                        $unitdetail->subunit_occupied_count = $occupied;
                        $unitdetail->subunit_vacant_count = $vacant;
                        $unitdetail->save();
                    }
                }
            }

            $contract->is_agreement_added = 0;
            $contract->save();

            $allUnitsVacant = $contract->contract_unit_details()->where('is_vacant', 1)->doesntExist();
            $allSubUnitvacant = $contract->contract_subunit_details()->where('is_vacant', 1)->doesntExist();
            // dd($allSubUnitvacant);
            if ($allUnitsVacant && $allSubUnitvacant) {
                $contract->has_agreement = 0;
                $contract->save();
            }
        });
    }


    public function terminate($data)
    {
        // dd($data);
        return DB::transaction(function () use ($data) {
            $agreement_id = $data['agreement_id'];
            $agreement = $this->find($agreement_id);

            // $agreement->deleted_by = auth()->user()->id;
            $agreement->terminated_by = auth()->user()->id;
            $agreement->terminated_reason = $data['terminated_reason'];
            $agreement->terminated_date = $data['terminated_date'];
            $agreement->agreement_status = 1;
            $agreement->save();

            $contract_id = $agreement->contract_id;
            // $contract = $this->contractRepository->find($contract_id);
            // $type = $contract->contract_type_id;
            // // $business_type = $contract->contract_unit->business_type;
            // $contract_unit_details = $agreement->agreement_units->pluck('contract_unit_details_id');
            // $contract_subunits = $agreement->agreement_units
            //     ->pluck('subunit_ids')
            //     ->flatten()
            //     ->unique()
            //     ->values()
            //     ->toArray();
            // dd($contract_subunits);
            // if ($type == 2) {
            //     foreach ($contract->contract_unit_details as $unit) {
            //         makeUnitVacant($unit, $contract_id);
            //     }
            // } else {
            //     foreach ($contract_unit_details as $unit) {
            //         $unitdetail = ContractUnitDetail::find($unit);
            //         if ($unitdetail) {
            //             $unitdetail->is_vacant = 0;
            //             $unitdetail->save();
            //         }
            //     }
            //     foreach ($contract_subunits as $contract_subunit_id) {
            //         $subunitdetail = ContractSubunitDetail::find($contract_subunit_id);
            //         if ($subunitdetail) {
            //             $subunitdetail->is_vacant = 0;
            //             $subunitdetail->save();
            //         }
            //     }
            // }

            // $contract = $this->contractRepository->find($contract_id);
            // $contract->is_agreement_added = 0;
            // $contract->save();

            // $anotherActiveAgreement = Agreement::where('contract_id', $contract_id)
            //     ->where('id', '!=', $agreement_id)
            //     ->where('agreement_status', 0)
            //     ->exists();
            // if (!$anotherActiveAgreement) {
            //     $contract->has_agreement = 0;
            //     $contract->save();
            // }

            $this->makeVacant($agreement_id, $contract_id);
            $this->updatePaymentDetails($agreement_id, $agreement->terminated_date);
            $insertdata = [
                'payment_amount' => $data['amount'],
                'payment_mode_id' => $data['payment_mode_id'],
                'bank_id' => $data['bank_id'],
                'cheque_number' => $data['cheque_number'],
                'transaction_type' => $data['transaction_type'],
                'agreement_payment_id' => 0,
                'agreement_unit_id' => 0,
                'agreement_id' => $data['agreement_id'],
                'payment_date' => $data['terminated_date'],
                'added_by' => auth()->user()->id
            ];
            $this->agreementPaymentDetailRepository->create($insertdata);

            return;
        });
    }
    public function updatePaymentDetails($agreement_id, $terminated_date)
    {
        $terminated_date = \Carbon\Carbon::parse($terminated_date);

        $paymentDetails = AgreementPaymentDetail::where('agreement_id', $agreement_id)
            ->whereDate('payment_date', '>', $terminated_date)
            ->get();
        // dd($paymentDetails);

        foreach ($paymentDetails as $payment) {
            $payment->terminate_status = 1;
            $payment->save();
        }

        return $paymentDetails;
    }
    public function getExpired(array $filters = [])
    {
        // dd($filters['end_date_from']);

        $oneMonthsLater = Carbon::today()->addMonths(1)->format('Y-m-d');
        // dd($oneMonthsLater);
        $query = Agreement::query()
            ->select([
                'agreements.*',
                'contracts.project_number',
                'companies.company_name',
                'agreement_tenants.tenant_name',
                'agreement_tenants.tenant_email',
                'agreement_tenants.tenant_mobile',
                'contract_types.contract_type',
                // \DB::raw('SUM(agreement_payment_details.paid_amount) as paid_amount')
                'contract_units.business_type as business_type'


            ])
            ->join('contracts', 'contracts.id', '=', 'agreements.contract_id')
            ->join('properties', 'properties.id', '=', 'contracts.property_id')
            // ->join('vendors', 'vendors.id', '=', 'contracts.vendor_id')
            ->join('companies', 'companies.id', '=', 'agreements.company_id')
            ->join('agreement_tenants', 'agreement_tenants.agreement_id', '=', 'agreements.id')
            ->join('contract_types', 'contract_types.id', '=', 'contracts.contract_type_id')
            ->join('contract_units', 'contract_units.contract_id', '=', 'contracts.id')
            // ->where('agreement_status', "=", 0)
            ->whereIn('agreement_status', [0, 2])
            ->where('end_date', '<=', $oneMonthsLater);

        // $get = $query->get();
        // dd($get);

        if (!empty($filters['search'])) {
            $query->orwhere('agreement_code', 'like', '%' . $filters['search'] . '%')
                ->orWhere('project_number', 'like', '%' . $filters['search'] . '%')

                ->orWhereHas('company', function ($q) use ($filters) {
                    $q->where('company_name', 'like', '%' . $filters['search'] . '%');
                })
                ->orWhereHas('contract.contract_type', function ($q) use ($filters) {
                    $q->where('contract_type', 'like', '%' . $filters['search'] . '%');
                })
                ->orWhereHas('contract.contract_unit', function ($q) use ($filters) {
                    $q->where('business_type', 'like', '%' . $filters['search'] . '%');
                })
                ->orWhereHas('tenant', function ($q) use ($filters) {
                    $q->where('tenant_name', 'like', '%' . $filters['search'] . '%')
                        ->orWhere('tenant_email', 'like', '%' . $filters['search'] . '%')
                        ->orWhere('tenant_mobile', 'like', '%' . $filters['search'] . '%');
                })
                // ->orWhereHas('contract_type', function ($q) use ($filters) {
                //     $q->where('contract_type', 'like', '%' . $filters['search'] . '%');
                // })

                ->orWhereRaw("CAST(agreements.id AS CHAR) LIKE ?", ['%' . $filters['search'] . '%']);
        }


        if (isset($filters['status']) && $filters['status'] !== 'all') {
            $query->where('agreements.agreement_status', $filters['status']);
        }

        if (!empty($filters['end_date_from'])) {
            $from = Carbon::createFromFormat('d-m-Y', $filters['end_date_from'])->format('Y-m-d');
            $query->whereDate('agreements.end_date', '>=', $from);
        }

        if (!empty($filters['end_date_to'])) {
            $to = Carbon::createFromFormat('d-m-Y', $filters['end_date_to'])->format('Y-m-d');
            $query->whereDate('agreements.end_date', '<=', $to);
        }



        // if (!empty($filters['company_id'])) {
        //     $query->Where('contracts.company_id', $filters['company_id']);
        // }

        $query->orderBy('agreements.id', 'desc');
        // $result = $query->get();
        // dd($result);

        return $query;
    }
    public function rentBifurcationStore($data)
    {
        DB::beginTransaction();

        try {
            $insertData = [];

            foreach ($data['bifurcation'] as $row) {

                // Update existing records
                if (!empty($row['id'])) {
                    agreementSubunitRentBifurcation::where('id', $row['id'])->update([
                        'rent_per_month' => $row['rent_per_month'],
                        'updated_by' => auth()->user()->id,
                        'updated_at' => now(),
                    ]);
                } else {
                    // Prepare new rows to insert
                    $insertData[] = [
                        'agreement_id' => $data['agreement_id'],
                        'agreement_unit_id' => $data['agreement_unit_id'],
                        'contract_unit_details_id' => $data['contract_unit_details_id'],
                        'contract_subunit_details_id' => $row['contract_subunit_details_id'],
                        'rent_per_month' => $row['rent_per_month'],
                        'added_by' => auth()->user()->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            // Insert all new rows at once
            if (!empty($insertData)) {
                agreementSubunitRentBifurcation::insert($insertData);
            }

            DB::commit();

            return [
                'status' => true,
                'message' => 'Rent bifurcation saved successfully'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'status' => false,
                'message' => 'Failed to save rent bifurcation',
                'error' => $e->getMessage()
            ];
        }
    }
    public function getAllAgreements()
    {
        return Agreement::with([
            'agreement_units:id,agreement_id,contract_unit_details_id',
            'tenant:id,tenant_name,agreement_id'
        ])
            ->select('id')
            ->get();
    }
}
