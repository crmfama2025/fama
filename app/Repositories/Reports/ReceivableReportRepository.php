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


    public function getQuery(array $filters = []): Builder
    {
        $twoWeeksBeforeEnd = Carbon::today()->addMonths(1)->subWeeks(2)->format('Y-m-d');
        // dd($twoWeeksBeforeEnd);

        // Get company IDs where user has finance.payable permission
        $permittedCompanyIds = getUserPermittedCompanyIds(auth()->id(), 'finance.receivable_cheque_clearing');

        $query = AgreementPaymentDetail::query()
            ->with(
                'agreementPayment.installment',
                'paymentMode',
                'bank',
                'agreement',
                'agreement.tenant',
                'agreement.contract',
                'agreement.contract.company',
                'agreement.contract.contract_type',
                'agreement.contract.contract_unit',
                'agreement.contract.contract_unit_details',
                'agreement.contract.property',
                'agreement.agreement_units.contractUnitDetail',
                'agreement.agreement_units.contractSubunitDetail',
                'agreement.agreement_units',
                'clearedReceivables',
                // Latest clearing information
                'latestClearedReceivable.paidMode',
                'latestClearedReceivable.paidBank',
                'latestClearedReceivable.paidCompany',

            )

            // ->where('id', '>=', 12)
            ->withSum('clearedReceivables as paid_amount_total', 'paid_amount')
            // ->where('is_payment_received', '!=', 1)
            ->where('terminate_status', 0);
        // ->whereDate('payment_date', '>=', Carbon::today())
        // ->whereDate('payment_date', '<=', Carbon::today()->addWeeks(2));

        // $query->whereHas('agreement.company', function ($q) use ($permittedCompanyIds) {
        //     $q->whereIn('company_id', $permittedCompanyIds);
        // });

        // Get the results
        // $results = $query->get();
        // dd($results);




        // $get = $query->get();
        // dd($get);

        if (!empty($filters['search'])) {
            $search = $filters['search'];


            $query->where(function ($q) use ($search) {
                $q->orWhere('payment_amount', 'like', '%' . $search . '%')
                    ->orWhere('payment_date', 'like', '%' . $search . '%')
                    ->orWhereHas('agreement.contract', function ($q2) use ($search) {
                        $q2->whereRaw("CONCAT('P - ', project_number) LIKE ?", ["%{$search}%"])
                            ->orWhereRaw("CONCAT('P-', project_number) LIKE ?", ["%{$search}%"])
                            ->orWhereRaw("CAST(project_number AS CHAR) LIKE ?", ["%{$search}%"]);
                    })
                    ->orWhereHas('agreement.contract.contract_type', function ($q2) use ($search) {
                        $q2->where('contract_type', 'like', "%$search%");
                    })
                    ->orWhereHas('agreement.contract.contract_unit', function ($q2) use ($search) {
                        $q2->where('business_type', 'like', "%$search%");
                    })
                    ->orWhereHas('agreement.tenant', function ($q2) use ($search) {
                        $q2->where('tenant_name', 'like', "%$search%")
                            ->orWhere('tenant_email', 'like', "%$search%")
                            ->orWhere('tenant_mobile', 'like', "%$search%");
                    })
                    ->orWhereHas('agreementUnit', function ($q2) use ($search) {
                        $q2->whereHas('contractUnitDetail', function ($q3) use ($search) {
                            $q3->where('unit_number', 'like', "%$search%");
                        });
                    })
                    ->orWhereHas('paymentMode', function ($q2) use ($search) {
                        $q2->where('payment_mode_name', 'like', "%$search%");
                    })
                    ->orWhereHas('agreement.contract.company', function ($q2) use ($search) {
                        $q2->where('company_name', 'like', "%$search%");
                    })
                    ->orWhereHas('agreement.contract.contract_type', function ($q2) use ($search) {
                        $q2->where('contract_type', 'like', "%$search%");
                    })
                    //  ->orWhereHas('agreement.agreement_units', function ($q2) use ($search) {
                    //     // get all agreement_unit IDs that match the unit number
                    //     $matchingUnitIds = AgreementUnit::whereHas('contractUnitDetail', function ($q2) use ($search) {
                    //         $q2->where('unit_number', 'like', "%$search%");
                    //     })->pluck('id');

                    //     $q2->whereIn('agreement_unit_id', $matchingUnitIds);
                    // })
                    ->orWhereHas('agreement.contract.property', function ($q2) use ($search) {
                        $q2->where('property_name', 'like', "%$search%");
                    })
                    // ->orWhereHas('agreement.contract', function ($q2) use ($search) {
                    //     // $q2->where('project_number', 'like', "%$search%");
                    //     $q2->whereRaw("CONCAT('P-',project_number) LIKE ?", "%$search%");
                    // })
                    ->orWhereRaw("CAST(agreement_payment_details.id AS CHAR) LIKE ?", ["%$search%"]);
            });
        }

        // Date filter
        if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
            $query->whereBetween('payment_date', [
                Carbon::createFromFormat('d-m-Y', $filters['date_from'])->format('Y-m-d'),
                Carbon::createFromFormat('d-m-Y', $filters['date_to'])->format('Y-m-d'),
            ]);
        }

        if (!empty($filters['unit_id'])) {
            // $query->whereHas('agreement.agreement_units', function ($q) use ($filters, &$unitIds) {
            //     $q->where('contract_unit_details_id', $filters['unit_id'])
            //         ->select('id'); // get agreement_unit IDs
            // });

            // // or simpler:
            // $query->whereIn('agreement_unit_id', function ($q) use ($filters) {
            //     $q->select('id')
            //         ->from('agreement_units')
            //         ->where('contract_unit_details_id', $filters['unit_id']);
            // });
            $query->whereHas('agreementUnit', function ($q) use ($filters) {
                $q->whereHas('contractUnitDetail', function ($q2) use ($filters) {
                    $q2->where('id', $filters['unit_id']);
                });
                // ALSO apply tenant condition if exists
                if (!empty($filters['tenant_id'])) {
                    $q->whereHas('agreement.tenant', function ($q3) use ($filters) {
                        $q3->where('id', $filters['tenant_id']);
                    });
                }
            });
        } elseif (!empty($filters['property_id'])) {
            $query->whereHas('agreement.contract.property', function ($q) use ($filters) {
                $q->where('id', $filters['property_id']);
            });
        }
        if (!empty($filters['mode_id'])) {
            $query->where('payment_mode_id', $filters['mode_id']);
        }
        if (!empty($filters['is_payment_received'])) {
            $query->where('is_payment_received', $filters['is_payment_received']);
        }
        if (!empty($filters['is_invoice_added'])) {
            $query->where('is_invoice_added', $filters['is_invoice_added']);
        }
        if (!empty($filters['paid_company_id'])) {
            $query->whereHas('latestClearedReceivable.paidCompany', function ($q) use ($filters) {
                $q->where('paid_company_id', $filters['paid_company_id']);
            });
        }

        if (!is_null($filters['company_id'])) {
            $query->whereHas('agreement.contract', function ($q) use ($filters) {
                $q->where('company_id', $filters['company_id']);
            });
        }
        if (empty($filters['unit_id']) && !empty($filters['tenant_id'])) {

            $query->whereHas('agreement.tenant', function ($q) use ($filters) {
                $q->where('id', $filters['tenant_id']);
            });
            // dd($filters['tenant_id']);
        }


        // $query->orderBy('agreement_payment_details.id', 'desc');
        // $results = $query->get();
        // dd($results->count());

        // $count = (clone $query)->count();
        // dd('2013 records found: ' . $count);



        return $query;
    }
}
