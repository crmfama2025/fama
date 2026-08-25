<?php

namespace App\Repositories\Reports;

use App\Models\ContractPaymentDetail;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class ContractReportRepository
{
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
}
