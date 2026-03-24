<?php

namespace App\Repositories\Sales;

use App\Models\Agreement;
use App\Models\AgreementDocument;
use App\Models\AgreementTenant;
use App\Models\Contract;
use App\Models\ContractSubunitDetail;
use App\Models\ContractUnitDetail;
use App\Models\SalesTenantAgreement;
use App\Models\SalesTenantUnit;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class TenantRegistrationRepository
{


    public function create(array $data)
    {
        return AgreementTenant::create($data);
    }
    public function update($id, array $data)
    {
        // dd($id, $data);
        $tenant = $this->find($id);
        // dd

        // dd($tenant);
        $tenant->update($data);
        return $tenant;
    }
    public function find($id)
    {
        // dd($id);
        return SalesTenantAgreement::findOrFail($id);
    }


    public function delete($id)
    {
        return DB::transaction(function () use ($id) {

            $agreement = $this->find($id);

            // 🔴 Get all agreement units
            $units = $agreement->agreementUnits;

            foreach ($units as $unit) {

                // ✅ B2C (single subunit)
                if ($agreement->business_type == 2) {

                    // 🔴 Unassign UNIT
                    if (!empty($unit->contract_unit_details_id)) {
                        ContractUnitDetail::where('id', $unit->contract_unit_details_id)
                            ->update(['is_sales_agreement_added' => 0]);
                    }

                    // 🔴 Unassign SUBUNIT
                    if (!empty($unit->contract_subunit_details_id)) {
                        ContractSubunitDetail::where('id', $unit->contract_subunit_details_id)
                            ->update(['is_sales_agreement_added' => 0]);
                    }
                } else {
                    // ✅ B2B (multiple subunits)

                    // 🔴 Unassign UNIT
                    if (!empty($unit->contract_unit_details_id)) {
                        ContractUnitDetail::where('id', $unit->contract_unit_details_id)
                            ->update(['is_sales_agreement_added' => 0]);
                    }

                    // 🔴 Unassign SUBUNITS (JSON)
                    if (!empty($unit->subunit_ids)) {
                        $subunitIds = json_decode($unit->subunit_ids, true);

                        if (!empty($subunitIds)) {
                            ContractSubunitDetail::whereIn('id', $subunitIds)
                                ->update(['is_sales_agreement_added' => 0]);
                        }
                    }
                }
            }

            // 🗑 Now delete agreement (triggers your booted() also)
            $agreement->delete();

            return true;
        });
    }
    public function getTenantsForAgreement()
    {
        return AgreementTenant::where('tenant_type', 1)->get();
    }
    public function getAgreementTenantsB2b()
    {
        $existingCustomers = AgreementTenant::with('tenantDocuments')->where('tenant_type', 1)
            ->where('id', '!=', 1)
            ->get(['id', 'tenant_name', 'tenant_type', 'tenant_code']);
        return $existingCustomers;
    }
    public function createUnit($data)
    {
        // dd($data);
        $unit = SalesTenantUnit::create($data);
        return $unit;
    }
    public function createSalesAgreement($data)
    {
        // dd($data);
        $agreement = SalesTenantAgreement::create($data);
        return $agreement;
    }
    public function getQuery(array $filters = [])
    {
        $query = SalesTenantAgreement::query()
            ->with([
                'tenant',
                'tenant.tenantDocuments',
                'agreementUnits.contractUnitDetail',
                'agreementUnits.contractSubunitDetail',
                'property',
            ]);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $searchLower = strtolower($search);
            $query->where(function ($q) use ($search, $searchLower) {
                $q->where('sales_agreement_code', 'like', "%{$search}%")
                    ->orWhereHas('tenant', function ($t) use ($search) {
                        $t->where('tenant_name', 'like', "%{$search}%")
                            ->orWhere('tenant_email', 'like', "%{$search}%")
                            ->orWhere('tenant_mobile', 'like', "%{$search}%")
                            ->ORwhereRaw("
                    CASE
                        WHEN tenant_type = 1 THEN 'B2B'
                        WHEN tenant_type = 2 THEN 'B2C'
                    END LIKE ?
                ", ["%{$search}%"]);
                    })
                    ->orWhereHas('property', function ($p) use ($search) {
                        $p->where('property_name', 'like', "%{$search}%");
                    })
                    ->orWhereRaw("
                    CASE
                        WHEN is_approved = 0 THEN 'Pending'
                        WHEN is_approved = 1 THEN 'Approved'
                        WHEN is_approved = 2 THEN 'Rejected'

                    END LIKE ?
                ", ["%{$search}%"]);
            });
        }

        return $query->latest();
    }
    public function updateUnit($data)
    {

        $id = $data['id'];
        unset($data['id']);
        $unit = SalesTenantUnit::find($id);
        // dd($unit);
        $unit->update($data);

        return $unit;
    }
    public function getAgreement($id)
    {
        $agreement = \App\Models\SalesTenantAgreement::with([
            'tenant.nationality',
            'tenant.tenantDocuments.TenantIdentity',
            'agreementUnits.contractUnitDetail.contractSubUnitDetails',
            'agreementUnits.unitType',
            'agreementUnits.salesTenantSubunitRents.contractSubUnitDetail',
            'agreementUnits.contract'
        ])->findOrFail($id);
        return $agreement;
    }
    public function approveOrReject($id, $data)
    {
        $tenant = SalesTenantAgreement::find($id);
        // dd

        // dd($tenant);
        $tenant->update($data);
        return $tenant;
    }
}
