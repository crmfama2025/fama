<?php

namespace App\Repositories\Reports;

use App\Models\Agreement;
use App\Models\AgreementPayment;
use App\Models\AgreementPaymentDetail;
use App\Models\ClearedReceivable;
use App\Models\ClearedReceivableAllocation;
use App\Models\Company;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReceivableReportRepository
{


    // public function getQuery(array $filters = []): Builder
    // {
    //     $twoWeeksBeforeEnd = Carbon::today()->addMonths(1)->subWeeks(2)->format('Y-m-d');
    //     // dd($twoWeeksBeforeEnd);

    //     // Get company IDs where user has finance.payable permission
    //     $permittedCompanyIds = getUserPermittedCompanyIds(auth()->id(), 'finance.receivable_cheque_clearing');

    //     $query = AgreementPaymentDetail::query()
    //         ->with(
    //             'agreementPayment.installment',
    //             'paymentMode',
    //             'bank',
    //             'agreement',
    //             'agreement.tenant',
    //             'agreement.contract',
    //             'agreement.contract.company',
    //             'agreement.contract.contract_type',
    //             'agreement.contract.contract_unit',
    //             'agreement.contract.contract_unit_details',
    //             'agreement.contract.property',
    //             'agreement.agreement_units.contractUnitDetail',
    //             'agreement.agreement_units.contractSubunitDetail',
    //             'agreement.agreement_units',
    //             'clearedReceivables',
    //             // Latest clearing information
    //             'latestClearedReceivable.paidMode',
    //             'latestClearedReceivable.paidBank',
    //             'latestClearedReceivable.paidCompany',

    //         )

    //         // ->where('id', '>=', 12)
    //         ->withSum('clearedReceivables as paid_amount_total', 'paid_amount')
    //         // ->where('is_payment_received', '!=', 1)
    //         ->where('terminate_status', 0);
    //     // ->whereDate('payment_date', '>=', Carbon::today())
    //     // ->whereDate('payment_date', '<=', Carbon::today()->addWeeks(2));

    //     // $query->whereHas('agreement.company', function ($q) use ($permittedCompanyIds) {
    //     //     $q->whereIn('company_id', $permittedCompanyIds);
    //     // });

    //     // Get the results
    //     // $results = $query->get();
    //     // dd($results);




    //     // $get = $query->get();
    //     // dd($get);

    //     if (!empty($filters['search'])) {
    //         $search = $filters['search'];


    //         $query->where(function ($q) use ($search) {
    //             $q->orWhere('payment_amount', 'like', '%' . $search . '%')
    //                 ->orWhere('payment_date', 'like', '%' . $search . '%')
    //                 ->orWhereHas('agreement.contract', function ($q2) use ($search) {
    //                     $q2->whereRaw("CONCAT('P - ', project_number) LIKE ?", ["%{$search}%"])
    //                         ->orWhereRaw("CONCAT('P-', project_number) LIKE ?", ["%{$search}%"])
    //                         ->orWhereRaw("CAST(project_number AS CHAR) LIKE ?", ["%{$search}%"]);
    //                 })
    //                 ->orWhereHas('agreement.contract.contract_type', function ($q2) use ($search) {
    //                     $q2->where('contract_type', 'like', "%$search%");
    //                 })
    //                 ->orWhereHas('agreement.contract.contract_unit', function ($q2) use ($search) {
    //                     $q2->where('business_type', 'like', "%$search%");
    //                 })
    //                 ->orWhereHas('agreement.tenant', function ($q2) use ($search) {
    //                     $q2->where('tenant_name', 'like', "%$search%")
    //                         ->orWhere('tenant_email', 'like', "%$search%")
    //                         ->orWhere('tenant_mobile', 'like', "%$search%");
    //                 })
    //                 ->orWhereHas('agreementUnit', function ($q2) use ($search) {
    //                     $q2->whereHas('contractUnitDetail', function ($q3) use ($search) {
    //                         $q3->where('unit_number', 'like', "%$search%");
    //                     });
    //                 })
    //                 ->orWhereHas('paymentMode', function ($q2) use ($search) {
    //                     $q2->where('payment_mode_name', 'like', "%$search%");
    //                 })
    //                 ->orWhereHas('agreement.contract.company', function ($q2) use ($search) {
    //                     $q2->where('company_name', 'like', "%$search%");
    //                 })
    //                 ->orWhereHas('agreement.contract.contract_type', function ($q2) use ($search) {
    //                     $q2->where('contract_type', 'like', "%$search%");
    //                 })
    //                 //  ->orWhereHas('agreement.agreement_units', function ($q2) use ($search) {
    //                 //     // get all agreement_unit IDs that match the unit number
    //                 //     $matchingUnitIds = AgreementUnit::whereHas('contractUnitDetail', function ($q2) use ($search) {
    //                 //         $q2->where('unit_number', 'like', "%$search%");
    //                 //     })->pluck('id');

    //                 //     $q2->whereIn('agreement_unit_id', $matchingUnitIds);
    //                 // })
    //                 ->orWhereHas('agreement.contract.property', function ($q2) use ($search) {
    //                     $q2->where('property_name', 'like', "%$search%");
    //                 })
    //                 // ->orWhereHas('agreement.contract', function ($q2) use ($search) {
    //                 //     // $q2->where('project_number', 'like', "%$search%");
    //                 //     $q2->whereRaw("CONCAT('P-',project_number) LIKE ?", "%$search%");
    //                 // })
    //                 ->orWhereRaw("CAST(agreement_payment_details.id AS CHAR) LIKE ?", ["%$search%"]);
    //         });
    //     }

    //     // Date filter
    //     if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
    //         $query->whereBetween('payment_date', [
    //             Carbon::createFromFormat('d-m-Y', $filters['date_from'])->format('Y-m-d'),
    //             Carbon::createFromFormat('d-m-Y', $filters['date_to'])->format('Y-m-d'),
    //         ]);
    //     }

    //     if (!empty($filters['unit_id'])) {
    //         // $query->whereHas('agreement.agreement_units', function ($q) use ($filters, &$unitIds) {
    //         //     $q->where('contract_unit_details_id', $filters['unit_id'])
    //         //         ->select('id'); // get agreement_unit IDs
    //         // });

    //         // // or simpler:
    //         // $query->whereIn('agreement_unit_id', function ($q) use ($filters) {
    //         //     $q->select('id')
    //         //         ->from('agreement_units')
    //         //         ->where('contract_unit_details_id', $filters['unit_id']);
    //         // });
    //         $query->whereHas('agreementUnit', function ($q) use ($filters) {
    //             $q->whereHas('contractUnitDetail', function ($q2) use ($filters) {
    //                 $q2->where('id', $filters['unit_id']);
    //             });
    //             // ALSO apply tenant condition if exists
    //             if (!empty($filters['tenant_id'])) {
    //                 $q->whereHas('agreement.tenant', function ($q3) use ($filters) {
    //                     $q3->where('id', $filters['tenant_id']);
    //                 });
    //             }
    //         });
    //     } elseif (!empty($filters['property_id'])) {
    //         $query->whereHas('agreement.contract.property', function ($q) use ($filters) {
    //             $q->where('id', $filters['property_id']);
    //         });
    //     }
    //     if (!empty($filters['mode_id'])) {
    //         $query->where('payment_mode_id', $filters['mode_id']);
    //     }
    //     if (!empty($filters['is_payment_received'])) {
    //         $query->where('is_payment_received', $filters['is_payment_received']);
    //     }
    //     if (!empty($filters['is_invoice_added'])) {
    //         $query->where('is_invoice_added', $filters['is_invoice_added']);
    //     }
    //     if (!empty($filters['paid_company_id'])) {
    //         $query->whereHas('latestClearedReceivable.paidCompany', function ($q) use ($filters) {
    //             $q->where('paid_company_id', $filters['paid_company_id']);
    //         });
    //     }

    //     if (!is_null($filters['company_id'])) {
    //         $query->whereHas('agreement.contract', function ($q) use ($filters) {
    //             $q->where('company_id', $filters['company_id']);
    //         });
    //     }
    //     if (empty($filters['unit_id']) && !empty($filters['tenant_id'])) {

    //         $query->whereHas('agreement.tenant', function ($q) use ($filters) {
    //             $q->where('id', $filters['tenant_id']);
    //         });
    //         // dd($filters['tenant_id']);
    //     }


    //     // $query->orderBy('agreement_payment_details.id', 'desc');
    //     // $results = $query->get();
    //     // dd($results->count());

    //     // $count = (clone $query)->count();
    //     // dd('2013 records found: ' . $count);



    //     return $query;
    // }


    public function getQuery(array $filters = [])
    {
        $userId = auth()->id();

        $permittedCompanyIds = getUserPermittedCompanyIds(
            $userId,
            'finance.receivable_cheque_clearing'
        );

        // Cleared receivables summary
        $cleared = DB::table('cleared_receivables')
            ->selectRaw('
            agreement_payment_details_id,
            SUM(paid_amount) AS paid_amount_total,
            MAX(paid_date) AS last_paid_date
        ')
            ->groupBy('agreement_payment_details_id');

        // Latest cleared receivable
        $latestClearIds = DB::table('cleared_receivables')
            ->selectRaw('
            agreement_payment_details_id,
            MAX(id) AS latest_clear_id
        ')
            ->groupBy('agreement_payment_details_id');

        // Installment position
        $composition = DB::table('agreement_payment_details')
            ->select([
                'id as payment_detail_id',
                'agreement_unit_id',
            ])
            ->selectRaw('
            ROW_NUMBER() OVER (
                PARTITION BY agreement_unit_id
                ORDER BY id
            ) AS installment_position
        ')
            ->selectRaw('
            COUNT(*) OVER (
                PARTITION BY agreement_unit_id
            ) AS installment_count
        ');
        // ->where('terminate_status', 0);

        // Main query
        $query = DB::table('agreement_payment_details as apd')
            ->whereNull('apd.deleted_at')
            ->leftJoin('agreements as a', function ($join) {
                $join->on('a.id', '=', 'apd.agreement_id')
                    ->whereNull('a.deleted_at');
            })
            ->leftJoin('agreement_payments as ap', function ($join) {
                $join->on('ap.id', '=', 'apd.agreement_payment_id')
                    ->whereNull('ap.deleted_at');
            })
            ->leftJoin('contracts as c', function ($join) {
                $join->on('c.id', '=', 'a.contract_id')
                    ->whereNull('c.deleted_at');
            })
            ->leftJoin('companies as co', function ($join) {
                $join->on('co.id', '=', 'c.company_id')
                    ->whereNull('co.deleted_at');
            })
            ->leftJoin(
                'contract_types as ct',
                'ct.id',
                '=',
                'c.contract_type_id'
            )
            ->leftJoin('contract_units as cu', function ($join) {
                $join->on('cu.contract_id', '=', 'c.id')
                    ->whereNull('cu.deleted_at');
            })
            ->leftJoin('properties as p', function ($join) {
                $join->on('p.id', '=', 'c.property_id')
                    ->whereNull('p.deleted_at');
            })
            ->leftJoin('agreement_tenants as t', function ($join) {
                $join->on('t.id', '=', 'a.tenant_id')
                    ->whereNull('t.deleted_at');
            })
            ->leftJoin('agreement_units as au', function ($join) {
                $join->on('au.id', '=', 'apd.agreement_unit_id')
                    ->whereNull('au.deleted_at');
            })
            ->leftJoin('contract_unit_details as cud', function ($join) {
                $join->on('cud.id', '=', 'au.contract_unit_details_id')
                    ->whereNull('cud.deleted_at');
            })
            ->leftJoin('contract_subunit_details as cusd', function ($join) {
                $join->on('cusd.id', '=', 'au.contract_subunit_details_id')
                    ->whereNull('cusd.deleted_at');
            })
            ->leftJoin(
                'payment_modes as pm',
                'pm.id',
                '=',
                'apd.payment_mode_id'
            )
            ->leftJoin(
                'banks as b',
                'b.id',
                '=',
                'apd.bank_id'
            )
            ->leftJoinSub(
                $cleared,
                'cleared',
                function ($join) {
                    $join->on(
                        'cleared.agreement_payment_details_id',
                        '=',
                        'apd.id'
                    );
                }
            )
            ->leftJoinSub(
                $latestClearIds,
                'latest',
                function ($join) {
                    $join->on(
                        'latest.agreement_payment_details_id',
                        '=',
                        'apd.id'
                    );
                }
            )
            ->leftJoin(
                'cleared_receivables as last_clear',
                'last_clear.id',
                '=',
                'latest.latest_clear_id'
            )
            ->leftJoin(
                'payment_modes as paid_pm',
                'paid_pm.id',
                '=',
                'last_clear.paid_mode_id'
            )
            ->leftJoin(
                'banks as paid_bank',
                'paid_bank.id',
                '=',
                'last_clear.paid_bank_id'
            )
            ->leftJoin(
                'companies as paid_company',
                'paid_company.id',
                '=',
                'last_clear.paid_company_id'
            )
            ->leftJoin(
                'installments as installment',
                'installment.id',
                '=',
                'ap.installment_id'
            )
            ->leftJoinSub(
                $composition,
                'composition',
                function ($join) {
                    $join->on(
                        'composition.payment_detail_id',
                        '=',
                        'apd.id'
                    );
                }
            )
            ->select([
                'apd.id',
                'c.project_number',
                'co.company_name',
                't.tenant_name',
                't.tenant_email',
                't.tenant_mobile',
                'p.property_name',
                'cud.unit_number',
                'cusd.subunit_no',
                'apd.payment_date',
                'apd.payment_amount',
                'apd.payment_mode_id',
                'apd.bank_id',
                'apd.cheque_number',
                'apd.bounced_date',
                'apd.bounced_reason',
                'pm.payment_mode_name',
                'b.bank_name',
                'installment.installment_name',
                'composition.installment_position',
                'composition.installment_count',
                'apd.is_payment_received',
                'apd.is_invoice_added',
                'apd.terminate_status',
                'apd.has_bounced',
                DB::raw('
                COALESCE(
                    cleared.paid_amount_total,
                    0
                ) AS paid_amount_total
            '),
                'last_clear.pending_amount',
                'last_clear.paid_date',
                'paid_pm.payment_mode_name AS paid_mode_name',
                'paid_bank.bank_name AS paid_bank_name',
                'paid_company.company_name AS paid_company_name',
                'last_clear.paid_cheque_number',
            ])
            // Base conditions
            // ->where('apd.terminate_status', 0)
            ->whereIn('c.company_id', $permittedCompanyIds);

        // Company
        if (
            isset($filters['company_id']) &&
            $filters['company_id'] !== null &&
            $filters['company_id'] !== ''
        ) {
            $query->where('c.company_id', $filters['company_id']);
        }

        // Contract
        if (!empty($filters['contract_id'])) {
            $query->where('c.id', $filters['contract_id']);
        }

        // Date from
        if (!empty($filters['date_from'])) {
            $dateFrom = Carbon::createFromFormat(
                'd-m-Y',
                $filters['date_from']
            )->format('Y-m-d');

            $query->where('apd.payment_date', '>=', $dateFrom);
        }

        // Date to
        if (!empty($filters['date_to'])) {
            $dateTo = Carbon::createFromFormat(
                'd-m-Y',
                $filters['date_to']
            )->format('Y-m-d');

            $query->where('apd.payment_date', '<=', $dateTo);
        }

        // Unit
        if (!empty($filters['unit_id'])) {
            $query->where('cud.id', $filters['unit_id']);
        }

        // Property
        if (!empty($filters['property_id'])) {
            $query->where('p.id', $filters['property_id']);
        }

        // Tenant
        if (!empty($filters['tenant_id'])) {
            $query->where('t.id', $filters['tenant_id']);
        }

        // Payment mode
        if (!empty($filters['mode_id'])) {
            $query->where('apd.payment_mode_id', $filters['mode_id']);
        }

        // Payment received
        if (
            isset($filters['is_payment_received']) &&
            $filters['is_payment_received'] !== ''
        ) {
            $query->where(
                'apd.is_payment_received',
                $filters['is_payment_received']
            );
        }

        // Invoice added
        if (
            isset($filters['is_invoice_added']) &&
            $filters['is_invoice_added'] !== ''
        ) {
            $query->where(
                'apd.is_invoice_added',
                $filters['is_invoice_added']
            );
        }

        // Paid company
        if (!empty($filters['paid_company_id'])) {
            $query->where(
                'last_clear.paid_company_id',
                $filters['paid_company_id']
            );
        }

        // Search
        if (!empty($filters['search'])) {
            $search = '%' . mb_strtolower(trim($filters['search'])) . '%';

            $query->where(function ($q) use ($search) {
                $q->whereRaw(
                    "LOWER(CAST(c.project_number AS CHAR)) LIKE ?",
                    [$search]
                )
                    ->orWhereRaw(
                        "LOWER(CONCAT('P - ', c.project_number)) LIKE ?",
                        [$search]
                    )
                    ->orWhereRaw(
                        'LOWER(co.company_name) LIKE ?',
                        [$search]
                    )
                    ->orWhereRaw(
                        'LOWER(t.tenant_name) LIKE ?',
                        [$search]
                    )
                    ->orWhereRaw(
                        'LOWER(t.tenant_email) LIKE ?',
                        [$search]
                    )
                    ->orWhereRaw(
                        'LOWER(t.tenant_mobile) LIKE ?',
                        [$search]
                    )
                    ->orWhereRaw(
                        'LOWER(p.property_name) LIKE ?',
                        [$search]
                    )
                    ->orWhereRaw(
                        'LOWER(cud.unit_number) LIKE ?',
                        [$search]
                    )
                    ->orWhereRaw(
                        'LOWER(cusd.subunit_no) LIKE ?',
                        [$search]
                    )
                    ->orWhereRaw(
                        'LOWER(pm.payment_mode_name) LIKE ?',
                        [$search]
                    )
                    ->orWhereRaw(
                        'LOWER(CAST(apd.payment_amount AS CHAR)) LIKE ?',
                        [$search]
                    )
                    ->orWhereRaw(
                        'LOWER(CAST(apd.id AS CHAR)) LIKE ?',
                        [$search]
                    );
            });
        }

        // Order
        $query->orderByDesc('apd.payment_date')
            ->orderByDesc('apd.id');

        return $query;
    }
}
