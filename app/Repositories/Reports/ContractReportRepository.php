<?php

namespace App\Repositories\Reports;

use App\Models\ContractPaymentDetail;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class ContractReportRepository
{
    // payable report
    public function getPayablesReport(array $filters = []): Builder
    {
        $userId = auth()->id();

        $permittedCompanyIds = getUserPermittedCompanyIds(
            $userId,
            'finance.payable_cheque_clearing'
        );

        $cleared = DB::table('contract_payable_clears')
            ->selectRaw('
                contract_payment_detail_id,
                SUM(
                    CASE
                        WHEN returned_status = 0 THEN paid_amount
                        ELSE 0
                    END
                ) AS total_paid,
                MAX(
                    CASE
                        WHEN returned_status = 0 THEN paid_date
                    END
                ) AS last_paid_date,
                COUNT(
                    CASE
                        WHEN returned_status = 0 THEN 1
                    END
                ) AS payment_count
            ')
            ->groupBy('contract_payment_detail_id');

        $latestClearIds = DB::table('contract_payable_clears')
            ->selectRaw('
                contract_payment_detail_id,
                MAX(id) AS latest_clear_id
            ')
            ->where('returned_status', 0)
            ->groupBy('contract_payment_detail_id');

        $composition = DB::table('contract_payment_details')
            ->select([
                'id as payment_detail_id',
                'contract_id',
            ])
            ->selectRaw('
                ROW_NUMBER() OVER (
                    PARTITION BY contract_id
                    ORDER BY id
                ) AS installment_position
            ')
            ->selectRaw('
                COUNT(*) OVER (
                    PARTITION BY contract_id
                ) AS installment_count
            ')
            ->whereNull('deleted_at');

        $query = DB::table('contract_payment_details as cpd')
            ->join('contracts as c', 'c.id', '=', 'cpd.contract_id')
            ->join('contract_payments as cp', 'cp.id', '=', 'cpd.contract_payment_id')
            ->join('contract_details as cd', 'cd.contract_id', '=', 'c.id')
            ->join('companies as co', 'co.id', '=', 'c.company_id')
            ->join('vendors as v', 'v.id', '=', 'c.vendor_id')
            ->join('properties as p', 'p.id', '=', 'c.property_id')
            ->join('areas as a', 'a.id', '=', 'c.area_id')
            ->join('localities as l', 'l.id', '=', 'c.locality_id')
            ->leftJoin('payment_modes as original_pm', 'original_pm.id', '=', 'cpd.payment_mode_id')
            ->leftJoin('banks as original_bank', 'original_bank.id', '=', 'cpd.bank_id')
            ->leftJoinSub($cleared, 'cleared', function ($join) {
                $join->on(
                    'cleared.contract_payment_detail_id',
                    '=',
                    'cpd.id'
                );
            })
            ->leftJoinSub($latestClearIds, 'latest', function ($join) {
                $join->on(
                    'latest.contract_payment_detail_id',
                    '=',
                    'cpd.id'
                );
            })
            ->leftJoin(
                'contract_payable_clears as last_clear',
                'last_clear.id',
                '=',
                'latest.latest_clear_id'
            )
            ->leftJoin(
                'payment_modes as paid_pm',
                'paid_pm.id',
                '=',
                'last_clear.paid_mode'
            )
            ->leftJoin(
                'banks as paid_bank',
                'paid_bank.id',
                '=',
                'last_clear.paid_bank'
            )
            ->leftJoin(
                'companies as paying_company',
                'paying_company.id',
                '=',
                'last_clear.company_id'
            )
            ->leftJoin(
                'contract_types as ct',
                'ct.id',
                '=',
                'c.contract_type_id'
            )
            ->leftJoin(
                'companies as indirect_company',
                'indirect_company.id',
                '=',
                'c.indirect_company_id'
            )
            ->leftJoinSub($composition, 'composition', function ($join) {
                $join->on(
                    'composition.payment_detail_id',
                    '=',
                    'cpd.id'
                );
            })
            ->select([
                'cpd.id as payment_detail_id',
                'cpd.contract_id',
                'c.project_number',
                'c.project_code',
                'c.contract_status',
                'co.company_name',
                'v.vendor_name',
                'a.area_name',
                'l.locality_name',
                'p.property_code',
                'p.property_name',
                'cd.start_date as contract_start_date',
                'cd.end_date as contract_end_date',

                'ct.contract_type',
                'ct.shortcode as contract_type_shortcode',

                'c.indirect_status',
                'c.indirect_company_id',
                'indirect_company.company_name as indirect_company_name',

                DB::raw("
                        CASE
                            WHEN c.indirect_status = 1 THEN 'Indirect'
                            ELSE 'Direct'
                        END AS contract_source
                    "),

                'composition.installment_position',
                'composition.installment_count',

                DB::raw("
                    CONCAT(
                        COALESCE(composition.installment_position, 0),
                        '/',
                        COALESCE(composition.installment_count, 0)
                    ) AS composition
                "),

                'cp.installment_id',
                'cp.interval',
                'cp.beneficiary',

                'cpd.payment_date as due_date',
                'cpd.payment_amount as payable_amount',
                'cpd.paid_status',
                'cpd.cheque_no',
                'cpd.cheque_issuer',
                'cpd.cheque_issuer_name',
                'cpd.has_returned',
                'cpd.returned_date',
                'cpd.returned_reason',
                'cpd.terminate_status',

                'original_pm.payment_mode_name as scheduled_payment_mode',
                'original_bank.bank_name as scheduled_bank',

                DB::raw('COALESCE(cleared.total_paid, 0) AS total_paid'),
                DB::raw(
                    'GREATEST(cpd.payment_amount - COALESCE(cleared.total_paid, 0), 0)
                                AS outstanding_amount'
                ),
                'cleared.last_paid_date',
                'cleared.payment_count',

                'last_clear.paid_date as latest_paid_date',
                'last_clear.paid_amount as latest_paid_amount',
                'last_clear.paid_cheque_number',
                'last_clear.payment_remarks',
                'paid_pm.payment_mode_name as paid_payment_mode',
                'paid_bank.bank_name as paid_bank_name',
                'paying_company.company_name as paying_company_name',
            ])
            ->whereNull('cpd.deleted_at')
            ->whereIn('c.company_id', $permittedCompanyIds)
            ->orderByDesc('cpd.payment_date');

        if (!empty($filters['company_id'])) {
            $query->where('c.company_id', $filters['company_id']);
        }

        if (!empty($filters['payment_status'])) {
            match ($filters['payment_status']) {
                'paid' => $query->whereRaw(
                    'COALESCE(cleared.total_paid, 0) >= cpd.payment_amount'
                ),
                'partial' => $query
                    ->whereRaw('COALESCE(cleared.total_paid, 0) > 0')
                    ->whereRaw(
                        'COALESCE(cleared.total_paid, 0) < cpd.payment_amount'
                    ),
                'unpaid' => $query->whereRaw(
                    'COALESCE(cleared.total_paid, 0) = 0'
                ),
                'overdue' => $query
                    ->whereDate('cpd.payment_date', '<', today())
                    ->whereRaw(
                        'COALESCE(cleared.total_paid, 0) < cpd.payment_amount'
                    ),
                default => null,
            };
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('cpd.payment_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('cpd.payment_date', '<=', $filters['date_to']);
        }

        if (!empty($filters['search'])) {
            $search = '%' . mb_strtolower(trim($filters['search'])) . '%';

            $query->where(function ($query) use ($search) {
                $query
                    ->whereRaw('LOWER(c.project_code) LIKE ?', [$search])
                    ->orWhereRaw(
                        "LOWER(CONCAT('P-', c.project_number)) LIKE ?",
                        [$search]
                    )
                    ->orWhereRaw('LOWER(co.company_name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(v.vendor_name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(p.property_name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(p.property_code) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(a.area_name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(l.locality_name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(cpd.cheque_no) LIKE ?', [$search])
                    ->orWhereRaw(
                        'LOWER(last_clear.paid_cheque_number) LIKE ?',
                        [$search]
                    );
            });
        }
        // dd($query->toRawSql());
        return $query;
    }
    // payable report


    // inventory report
    public function getInventoryReport(array $filters = []): Builder
    {
        $userId = auth()->id();

        $permittedCompanyIds = getUserPermittedCompanyIds(
            $userId,
            'finance.payable_cheque_clearing'
        );

        $subunitTypeSql = "
            CONCAT_WS(
                ', ',
                CASE WHEN cu.partition = 1 THEN 'Partition' END,
                CASE WHEN cu.bedspace = 1 THEN 'Bedspace' END,
                CASE WHEN cu.room = 1 THEN 'Room' END,
                CASE
                    WHEN cu.partition = 0
                        AND cu.bedspace = 0
                        AND cu.room = 0
                        AND (
                            FIND_IN_SET('4', REPLACE(cu.subunittype, ' ', '')) > 0
                            OR cu.rent_per_flat > 0
                        )
                    THEN 'Full Flat'
                END
            )
        ";

        $query = DB::table('contract_unit_details as cu')
            ->join('contracts as c', 'c.id', '=', 'cu.contract_id')
            ->leftJoin('unit_types as ut', 'ut.id', '=', 'cu.unit_type_id')
            ->leftJoin('property_types as pt', 'pt.id', '=', 'cu.property_type_id')
            ->leftJoin('unit_statuses as us', 'us.id', '=', 'cu.unit_status_id')
            ->join('contract_details as cd', 'cd.contract_id', '=', 'c.id')
            ->join('companies as co', 'co.id', '=', 'c.company_id')
            ->leftJoin('vendors as v', 'v.id', '=', 'c.vendor_id')
            ->leftJoin('properties as p', 'p.id', '=', 'c.property_id')
            ->leftJoin('areas as a', 'a.id', '=', 'c.area_id')
            ->leftJoin('localities as l', 'l.id', '=', 'c.locality_id')
            ->select([
                'cu.id as contract_unit_detail_id',
                'cu.contract_unit_id',
                'c.id as contract_id',
                'c.project_number',
                'c.project_code',
                'c.contract_status',
                DB::raw("
                    CASE
                        WHEN c.parent_contract_id IS NOT NULL
                            AND c.parent_contract_id > 0
                        THEN 'Renewal'
                        ELSE 'New'
                    END AS renewal_status
                "),
                'co.company_name',
                'v.vendor_name',
                'p.property_code',
                'p.property_name',
                'a.area_name',
                'l.locality_name',
                'cd.start_date as contract_start_date',
                'cd.end_date as contract_end_date',
                'cu.unit_number',
                'ut.unit_type',
                'cu.maid_room',
                'pt.property_type',
                'cu.floor_no as floor_number',
                'us.unit_status',
                'cu.unit_rent_per_annum',
                'cu.total_rent_per_unit_per_month as unit_rent_per_month',

                DB::raw("{$subunitTypeSql} AS partition_bedspace_room"),

                DB::raw("
                CONCAT_WS(
                    ', ',
                    CASE WHEN cu.partition = 1 THEN cu.total_partition END,
                    CASE WHEN cu.bedspace = 1 THEN cu.total_bedspace END,
                    CASE WHEN cu.room = 1 THEN cu.total_room END,
                    CASE
                        WHEN cu.partition = 0
                            AND cu.bedspace = 0
                            AND cu.room = 0
                            AND (
                                FIND_IN_SET('4', REPLACE(cu.subunittype, ' ', '')) > 0
                                OR cu.rent_per_flat > 0
                            )
                        THEN 1
                    END
                ) AS no_of_partition_bedspace_room
            "),

                DB::raw("
                CONCAT_WS(
                    ' - ',
                    CASE
                        WHEN cu.partition = 1
                        THEN CONCAT('AED ', FORMAT(cu.rent_per_partition, 2))
                    END,
                    CASE
                        WHEN cu.bedspace = 1
                        THEN CONCAT('AED ', FORMAT(cu.rent_per_bedspace, 2))
                    END,
                    CASE
                        WHEN cu.room = 1
                        THEN CONCAT('AED ', FORMAT(cu.rent_per_room, 2))
                    END,
                    CASE
                        WHEN cu.partition = 0
                            AND cu.bedspace = 0
                            AND cu.room = 0
                            AND (
                                FIND_IN_SET('4', REPLACE(cu.subunittype, ' ', '')) > 0
                                OR cu.rent_per_flat > 0
                            )
                        THEN CONCAT('AED ', FORMAT(cu.rent_per_flat, 2))
                    END
                ) AS rent_per_partition_bedspace_room
            "),

                'cu.rent_per_flat',
                'cu.unit_profit_perc as unit_profit_percentage',
                'cu.unit_profit',
                'cu.unit_revenue',
                'cu.partition',
                'cu.bedspace',
                'cu.room',
                'cu.total_partition',
                'cu.total_bedspace',
                'cu.total_room',
                'cu.rent_per_partition',
                'cu.rent_per_bedspace',
                'cu.rent_per_room',
                'cu.rent_per_unit_per_annum',
                'cu.total_rent_per_unit_per_month',
                'cu.is_vacant',
                'cu.is_sales_agreement_added',
                'cu.unit_amount_payable',
                'cu.unit_commission',
                'cu.unit_deposit',
                'cu.unit_rent_per_month as stored_unit_rent_per_month',
                'cu.subunit_occupied_count',
                'cu.subunit_vacant_count',
                'cu.total_payment_received',
                'cu.total_payment_pending',
                'cu.discount',
            ])
            ->whereIn('c.company_id', $permittedCompanyIds)
            ->whereNull('cu.deleted_at')
            ->orderByDesc('c.project_number')
            ->orderBy('cu.unit_number');

        if (!empty($filters['company_id'])) {
            $query->where('c.company_id', $filters['company_id']);
        }

        if (!empty($filters['vendor_id'])) {
            $query->where('c.vendor_id', $filters['vendor_id']);
        }

        if (!empty($filters['property_id'])) {
            $query->where('c.property_id', $filters['property_id']);
        }

        if (!empty($filters['area_id'])) {
            $query->where('c.area_id', $filters['area_id']);
        }

        if (!empty($filters['locality_id'])) {
            $query->where('c.locality_id', $filters['locality_id']);
        }

        if (isset($filters['contract_status']) && $filters['contract_status'] !== '') {
            $query->where('c.contract_status', $filters['contract_status']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('cd.start_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('cd.end_date', '<=', $filters['date_to']);
        }

        if (!empty($filters['search'])) {
            $search = '%' . mb_strtolower(trim($filters['search'])) . '%';

            $query->where(function (Builder $query) use ($search, $subunitTypeSql) {
                $query->whereRaw('LOWER(c.project_code) LIKE ?', [$search])
                    ->orWhereRaw("LOWER(CONCAT('P-', c.project_number)) LIKE ?", [$search])
                    ->orWhereRaw('LOWER(co.company_name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(v.vendor_name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(p.property_code) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(p.property_name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(a.area_name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(l.locality_name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(cu.unit_number) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(ut.unit_type) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(pt.property_type) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(cu.floor_no) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(us.unit_status) LIKE ?', [$search])
                    ->orWhereRaw("LOWER({$subunitTypeSql}) LIKE ?", [$search])
                    ->orWhereRaw("LOWER(
                                CASE WHEN c.parent_contract_id IS NOT NULL
                                    AND c.parent_contract_id > 0
                                THEN 'Renewal'
                                ELSE 'New'
                            END) LIKE ?", [$search])
                    ->orWhereRaw(
                        "DATE_FORMAT(cd.start_date, '%d/%m/%Y') LIKE ?",
                        [$search]
                    )
                    ->orWhereRaw(
                        "DATE_FORMAT(cd.end_date, '%d/%m/%Y') LIKE ?",
                        [$search]
                    );
            });
        }

        // dd($query->toRawSql());
        return $query;
    }
    // inventory report




    // Occupancy report
    public function getOccupancyReport(array $filters = []): Builder
    {
        $permittedCompanyIds = getUserPermittedCompanyIds(
            auth()->id(),
            'finance.payable_cheque_clearing'
        );

        $occupancyStatusSql = "CASE WHEN su.is_vacant = 1 THEN 'Vacant' ELSE 'Occupied' END";
        $subunitTypeSql = "CASE su.subunit_type
                                WHEN 1 THEN 'Partition'
                                WHEN 2 THEN 'Bedspace'
                                WHEN 3 THEN 'Room'
                                WHEN 4 THEN 'Full Flat'
                                ELSE 'Unknown'
                            END";

        $query = DB::table('contract_subunit_details as su')
            ->join('contract_unit_details as cu', 'cu.id', '=', 'su.contract_unit_detail_id')
            ->join('contracts as c', 'c.id', '=', 'su.contract_id')
            ->join('contract_details as cd', 'cd.contract_id', '=', 'c.id')
            ->join('companies as co', 'co.id', '=', 'c.company_id')
            ->leftJoin('vendors as v', 'v.id', '=', 'c.vendor_id')
            ->leftJoin('properties as p', 'p.id', '=', 'c.property_id')
            ->leftJoin('areas as a', 'a.id', '=', 'c.area_id')
            ->leftJoin('localities as l', 'l.id', '=', 'c.locality_id')
            ->leftJoin('unit_types as ut', 'ut.id', '=', 'cu.unit_type_id')
            ->leftJoin('property_types as pt', 'pt.id', '=', 'cu.property_type_id')
            ->leftJoin('unit_statuses as us', 'us.id', '=', 'cu.unit_status_id')
            ->select([
                'su.id as subunit_id',
                'su.contract_id',
                'su.contract_unit_id',
                'su.contract_unit_detail_id',
                'c.project_number',
                'c.project_code',
                'co.company_name',
                'v.vendor_name',
                'p.property_code',
                'p.property_name',
                'a.area_name',
                'l.locality_name',
                'cd.start_date as contract_start_date',
                'cd.end_date as contract_end_date',
                'cu.unit_number',
                'ut.unit_type',
                'cu.maid_room',
                'pt.property_type',
                'cu.floor_no as floor_number',
                'us.unit_status',
                'cu.unit_rent_per_annum',
                'cu.rent_per_unit_per_month as unit_rent_per_month',
                'cu.rent_per_flat',
                'cu.unit_profit_perc as unit_profit_percentage',
                'cu.unit_profit',
                'cu.unit_revenue',
                'su.subunit_no',
                'su.subunit_code',
                'su.subunit_type',
                DB::raw("{$subunitTypeSql} AS subunit_type_name"),
                'su.is_vacant',
                DB::raw("{$occupancyStatusSql} AS occupancy_status"),
                'su.is_sales_agreement_added',
                'su.added_by',
                'su.updated_by',
                'su.deleted_by',
                'su.created_at as subunit_created_at',
                'su.updated_at as subunit_updated_at',
            ])
            ->whereIn('c.company_id', $permittedCompanyIds)
            ->whereNull('cu.deleted_at')
            ->whereNull('su.deleted_at')
            ->orderByDesc('c.project_number')
            ->orderBy('cu.unit_number')
            ->orderBy('su.subunit_no');

        foreach (
            [
                'company_id' => 'c.company_id',
                'vendor_id' => 'c.vendor_id',
                'property_id' => 'c.property_id',
                'area_id' => 'c.area_id',
                'locality_id' => 'c.locality_id',
                'subunit_type' => 'su.subunit_type'
            ] as $filter => $column
        ) {
            if (isset($filters[$filter]) && $filters[$filter] !== '') {
                $query->where($column, $filters[$filter]);
            }
        }

        if (isset($filters['occupancy_status']) && $filters['occupancy_status'] !== '') {
            $query->where('su.is_vacant', $filters['occupancy_status'] === 'vacant' ? 1 : 0);
        }
        if (!empty($filters['date_from'])) {
            $query->whereDate('cd.end_date', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('cd.start_date', '<=', $filters['date_to']);
        }
        if (!empty($filters['search'])) {
            $search = '%' . mb_strtolower(trim($filters['search'])) . '%';
            $query->where(function (Builder $query) use ($search, $occupancyStatusSql, $subunitTypeSql) {
                $query->whereRaw("LOWER(CONCAT('P-', c.project_number)) LIKE ?", [$search])
                    ->orWhereRaw('LOWER(c.project_code) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(co.company_name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(v.vendor_name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(p.property_name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(a.area_name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(l.locality_name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(cu.unit_number) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(su.subunit_no) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(su.subunit_code) LIKE ?', [$search])
                    ->orWhereRaw("LOWER({$subunitTypeSql}) LIKE ?", [$search])
                    ->orWhereRaw("LOWER({$occupancyStatusSql}) LIKE ?", [$search]);
            });
        }

        return $query;
    }
    // Occupancy report
}
