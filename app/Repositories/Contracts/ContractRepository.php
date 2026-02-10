<?php

namespace App\Repositories\Contracts;

use App\Models\Contract;
use App\Models\ContractUnitDetail;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ContractRepository
{
    public function all()
    {
        return Contract::all();
    }

    public function find($id)
    {

        $contract =  Contract::with(
            'contract_detail',
            'contract_rentals.installment',
            'contract_documents',
            'contract_otc',
            'contract_payments.contractPaymentDetails.payment_mode',
            'contract_payments.contractPaymentDetails.bank',
            'contract_payments.installment',
            'contract_unit.contractUnitDetails.contractSubUnitDetails',
            'contract_unit_details.property_type',
            'contract_unit_details.unit_type',
            'contract_unit_details.unit_status',
            'company',
            'area',
            'locality',
            'vendor',
            'property',
            'contract_type',
            'children',
            'parent',
            'contract_scope'
        )->findOrFail($id);

        return $contract;
    }

    public function getAllDataById($id)
    {
        $contract = $this->find($id);
        // Get unique values per column for this project
        $totals = ContractUnitDetail::select(
            DB::raw('MAX(rent_per_partition) as rent_per_partition'),
            DB::raw('MAX(rent_per_bedspace) as rent_per_bedspace'),
            DB::raw('MAX(rent_per_room) as rent_per_room'),
            DB::raw('MAX(rent_per_flat) as rent_per_flat')
        )
            ->where('contract_id', $id)
            ->first();


        // Merge totals into the contract object
        $contract->totals = [
            'prj_rent_per_partition' => $totals->rent_per_partition ?? 0,
            'prj_rent_per_bedspace'  => $totals->rent_per_bedspace ?? 0,
            'prj_rent_per_room'      => $totals->rent_per_room ?? 0,
            'prj_rent_per_flat'      => $totals->rent_per_flat ?? 0,
        ];

        return $contract;
    }

    public function findId($data)
    {
        return Contract::where($data)->first();
    }

    public function create(array $data)
    {
        // dd($data);
        return Contract::create($data);
    }

    public function update($id, array $data)
    {
        $contract = $this->find($id);
        $contract->update($data);
        return $contract;
    }
    public function delete($id)
    {
        $contract = $this->find($id);
        $contract->deleted_by = auth()->user()->id;
        $contract->save();
        return $contract->delete();
    }

    // public function checkIfExist($data)
    // {
    //     $existing = Contract::withTrashed()
    //         ->where('contract_name', $data['contract_name'])
    //         ->first();

    //     if ($existing && $existing->trashed()) {
    //         // $existing->restore();
    //         return $existing;
    //     }
    // }

    public function getQuery(array $filters = []): Builder
    {

        $query = Contract::query()
            ->select([
                'contracts.*',
                'properties.property_name',
                'vendors.vendor_name',
                'contract_details.start_date',
                'contract_details.end_date',
                'companies.company_name',
                'contract_units.no_of_units',
                'contract_units.business_type',
                'contract_rentals.roi_perc',
                'contract_rentals.expected_profit',
                // $statusText
            ])
            ->join('contract_details', 'contract_details.contract_id', '=', 'contracts.id')
            ->join('properties', 'properties.id', '=', 'contracts.property_id')
            ->join('vendors', 'vendors.id', '=', 'contracts.vendor_id')
            ->join('localities', 'localities.id', '=', 'contracts.locality_id')
            ->join('areas', 'areas.id', '=', 'contracts.area_id')
            ->join('companies', 'companies.id', '=', 'contracts.company_id')
            ->join('contract_types', 'contract_types.id', '=', 'contracts.contract_type_id')
            ->leftJoin('contract_units', 'contract_units.contract_id', '=', 'contracts.id')
            ->leftJoin('contract_rentals', 'contract_rentals.contract_id', '=', 'contracts.id');

        if (!empty($filters['search'])) {

            $search = trim($filters['search']);
            $searchLike = str_replace('-', '%', $search);

            $query->orwhere('project_code', 'like', '%' . $filters['search'] . '%')
                ->orWhere('project_number', 'like', '%' . $filters['search'] . '%')

                ->orWhereHas('company', function ($q) use ($filters) {
                    $q->where('company_name', 'like', '%' . $filters['search'] . '%');
                })
                ->orWhereHas('vendor', function ($q) use ($filters) {
                    $q->where('vendor_name', 'like', '%' . $filters['search'] . '%');
                })
                ->orWhereHas('contract_type', function ($q) use ($filters) {
                    $q->where('contract_type', 'like', '%' . $filters['search'] . '%')
                        ->orWhere('shortcode', 'like', '%' . $filters['search'] . '%');
                })
                ->orWhereHas('contract_detail', function ($q) use ($filters, $searchLike) {
                    $q->where('start_date', 'like', "%{$searchLike}%")
                        ->orWhere('end_date', 'like', "%{$searchLike}%");
                })
                ->orWhereHas('contract_unit', function ($q) use ($filters) {
                    $q->whereRaw("
                    CASE
                        WHEN business_type = 1 THEN 'B2B'
                        WHEN business_type = 2 THEN 'B2C'
                    END LIKE ?
                ", ['%' . $filters['search'] . '%']);
                })
                ->orWhereHas('contract_rentals', function ($q) use ($filters) {
                    $q->where('roi_perc', 'like', '%' . $filters['search'] . '%')
                        ->orWhere('expected_profit', 'like', '%' . $filters['search'] . '%');
                })
                ->orWhereHas('locality', function ($q) use ($filters) {
                    $q->where('locality_name', 'like', '%' . $filters['search'] . '%');
                })
                ->orWhereHas('property', function ($q) use ($filters) {
                    $q->where('property_name', 'like', '%' . $filters['search'] . '%');
                })
                ->orWhereRaw("
                    CASE
                        WHEN contract_status = 0 THEN 'Pending'
                        WHEN contract_status = 1 THEN 'Processing'
                        WHEN contract_status = 2 THEN 'Approved'
                        WHEN contract_status = 3 THEN 'Rejected'
                        WHEN contract_status = 4 THEN 'Approval Pending'
                        WHEN contract_status = 5 THEN 'Approval on Hold'
                        WHEN contract_status = 6 THEN 'Partially Signed'
                        WHEN contract_status = 7 THEN 'Fully Signed'
                        WHEN contract_status = 8 THEN 'Expired'
                        WHEN contract_status = 9 THEN 'Terminated'
                    END LIKE ?
                ", ['%' . $filters['search'] . '%'])
                ->orWhereRaw("CAST(contracts.id AS CHAR) LIKE ?", ['%' . $filters['search'] . '%']);
        }



        if (!empty($filters['company_id'])) {
            $query->Where('contracts.company_id', $filters['company_id']);
        }

        return $query;
    }

    public function allwithUnits()
    {
        return Contract::with([
            'contract_unit.contractUnitDetails' => function ($q) {
                $q->where('is_vacant', 0)
                    ->orWhereHas('contractSubUnitDetails', function ($subQ) {
                        $subQ->where('is_vacant', 0);
                    })
                    ->with(['contractSubUnitDetails' => function ($subQ) {
                        $subQ->where('is_vacant', 0);
                    }]);
            },
            'contract_detail',
            'contract_payment_receivables',
            'contract_rentals'
        ])
            ->withCount('contract_payment_receivables')
            ->withSum('contract_payment_receivables', 'receivable_amount')
            ->where('contract_status', 7)
            ->where('is_agreement_added', 0)
            ->get();
    }
    public function fullContracts()
    {
        return Contract::with([
            'contract_unit.contractUnitDetails.contractSubunitDetails',
            'contract_detail',
            'contract_payment_receivables',
            'contract_rentals'
        ])
            ->withCount('contract_payment_receivables')
            ->withSum('contract_payment_receivables', 'receivable_amount')
            ->where('contract_status', 7)
            // ->where('is_agreement_added', 0)
            ->get();
    }

    public function getRenewalQuery(array $filters = []): Builder
    {
        $twoMonthsLater = Carbon::today()->addMonths(2)->format('Y-m-d');


        $query = Contract::query()
            ->select([
                'contracts.*',
                'contract_types.contract_type',
                'contract_details.end_date',
                'companies.company_name',
                'vendors.vendor_name'
            ])
            ->join('contract_details', 'contract_details.contract_id', '=', 'contracts.id')
            ->join('vendors', 'vendors.id', '=', 'contracts.vendor_id')
            ->join('companies', 'companies.id', '=', 'contracts.company_id')
            ->join('contract_types', 'contract_types.id', '=', 'contracts.contract_type_id')
            ->where('contract_renewal_status', '!=', '1')
            ->where('renew_reject_status', '=', '0')
            ->where('contract_status', '>=', '7')
            ->whereHas('contract_detail', function ($q) use ($twoMonthsLater) {
                $q->where('end_date', '<=', $twoMonthsLater);
            });



        if (!empty($filters['search'])) {
            $query->orwhere('project_code', 'like', '%' . $filters['search'] . '%')
                ->orWhere('project_number', 'like', '%' . $filters['search'] . '%')

                ->orWhereHas('company', function ($q) use ($filters) {
                    $q->where('company_name', 'like', '%' . $filters['search'] . '%');
                })
                ->orWhereHas('contract_rentals', function ($q) use ($filters) {
                    $q->where('roi_perc', 'like', '%' . $filters['search'] . '%')
                        ->orWhere('expected_profit', 'like', '%' . $filters['search'] . '%');
                })
                ->orWhereHas('contract_unit', function ($q) use ($filters) {
                    $q->where('no_of_units', 'like', '%' . $filters['search'] . '%');
                })
                ->orWhereHas('contract_type', function ($q) use ($filters) {
                    $q->where('contract_type', 'like', '%' . $filters['search'] . '%');
                })
                ->orWhereHas('locality', function ($q) use ($filters) {
                    $q->where('locality_name', 'like', '%' . $filters['search'] . '%');
                })
                ->orWhereHas('property', function ($q) use ($filters) {
                    $q->where('property_name', 'like', '%' . $filters['search'] . '%');
                })
                ->orWhereRaw("CAST(contracts.id AS CHAR) LIKE ?", ['%' . $filters['search'] . '%']);
        }


        if (!empty($filters['company_id'])) {
            $query->Where('contracts.company_id', $filters['company_id']);
        }

        // dd($query);

        return $query;
    }

    public function updateRejectRenew($contract_id, $data)
    {
        DB::enableQueryLog();

        $contract = $this->find($contract_id);
        $contract->update($data);

        return $contract;
    }

    public function getAllRelatedContracts($contract_id, $visited = [])
    {
        // Prevent infinite recursion by tracking visited IDs
        if (in_array($contract_id, $visited)) {
            return collect();
        }

        $visited[] = $contract_id;

        $contract = $this->find($contract_id);
        if (!$contract) {

            return collect();
        }

        $renewals = collect();

        // Load relations only once
        $contract->loadMissing([
            'contract_detail',
            'contract_rentals',
        ]);

        if ($contract->parent_contract_id != NULL || $contract->contract_renewal_status != '0') {
            // Add current contract
            $renewals->push($contract);
        }

        // Recursively include parent
        if ($contract->parent) {
            $renewals = $renewals->merge(
                $this->getAllRelatedContracts($contract->parent->id, $visited)
            );
        }

        // Recursively include children
        foreach ($contract->children as $child) {
            $renewals = $renewals->merge(
                $this->getAllRelatedContracts($child->id, $visited)
            );
        }

        return $renewals->unique('id')->values();
    }

    public function terminate(array $data)
    {
        $contractId = $data['contract_id'];
        $data = [
            'contract_status'           => 9,
            'terminated_date'   => $data['terminated_date'],
            'terminated_reason' => $data['terminated_reason'],
            'terminated_by' => $data['user_id'],
            'balance_amount'   => $data['balance_amount'],
            'balance_received' => $data['balance_received'] ?? 0,
            'updated_at'       => now()
        ];


        $contract = $this->find($contractId);
        $contract->update($data);
        return $contract;
    }
}
