<?php

namespace App\Repositories;

use App\Models\Agreement;
use App\Models\AgreementPayment;
use App\Models\AgreementPaymentDetail;
use App\Models\ClearedReceivable;
use App\Models\Company;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TenantChequeRepository
{

    // public function getQuery(array $filters = []): Builder
    // {
    //     $twoWeeksBeforeEnd = Carbon::today()->addMonths(1)->subWeeks(2)->format('Y-m-d');
    //     // dd($twoWeeksBeforeEnd);
    //     $query = AgreementPaymentDetail::query()
    //         ->with(
    //             'agreementPayment.installment',
    //             'paymentMode',
    //             'bank',
    //             'agreement',
    //             'agreement.tenant',
    //             'agreement.contract',
    //             'agreement.contract.contract_type',
    //             'agreement.contract.contract_unit',
    //             'agreement.contract.contract_unit_details',
    //             'agreement.contract.property',
    //             'agreement.agreement_units.contractUnitDetail'
    //         )

    //         ->where('terminate_status', 0)
    //         ->whereDate('payment_date', '>=', Carbon::today())
    //         ->whereDate('payment_date', '<=', Carbon::today()->addWeeks(2));
    //     // ->whereNull('agreement_payment_details.deleted_at');

    //     // $query = AgreementPaymentDetail::query()
    //     //     ->with('agreementPayment.installment', 'paymentMode', 'bank')
    //     //     ->select([
    //     //         'agreement_payment_details.*',
    //     //         'agreement_payment_details.id as apd_id',
    //     //         'agreement_payment_details.agreement_id',
    //     //         'agreement_payment_details.paid_amount',
    //     //         'agreement_payment_details.payment_date',
    //     //         // add other columns from agreement_payment_details you need
    //     //         'ag.id as agreement_id_alias',
    //     //         'ag.agreement_code',
    //     //         'ct.id as contract_id',
    //     //         'ct.project_number',
    //     //         'ct.contract_type_id',
    //     //         'cm.company_name',
    //     //         'at.tenant_name',
    //     //         'at.tenant_email',
    //     //         'at.tenant_mobile',
    //     //         'cty.contract_type',
    //     //         'cu.business_type as business_type',
    //     //         'pt.*',
    //     //         'ctu.unit_number'
    //     //     ])
    //     //     ->join('agreements as ag', 'ag.id', '=', 'agreement_payment_details.agreement_id')
    //     //     ->join('contracts as ct', 'ct.id', '=', 'ag.contract_id')
    //     //     ->join('properties as pt', 'pt.id', '=', 'ct.property_id')
    //     //     ->join('companies as cm', 'cm.id', '=', 'ag.company_id')
    //     //     ->join('agreement_tenants as at', 'at.agreement_id', '=', 'ag.id')
    //     //     ->join('contract_types as cty', 'cty.id', '=', 'ct.contract_type_id')
    //     //     ->join('contract_units as cu', 'cu.contract_id', '=', 'ct.id')
    //     //     ->join('agreement_units as au', 'au.id', '=', 'agreement_payment_details.agreement_unit_id')
    //     //     ->join('contract_unit_details as ctu', 'ctu.id', '=', 'au.contract_unit_details_id')
    //     //     ->where('terminate_status', 0)
    //     //     ->whereNull('agreement_payment_details.deleted_at');


    //     // Get the results
    //     $results = $query->get();
    //     // dd($results);




    //     // $get = $query->get();
    //     // dd($get);

    //     // if (!empty($filters['search'])) {
    //     //     $query->orwhere('agreement_code', 'like', '%' . $filters['search'] . '%')
    //     //         ->orWhere('project_number', 'like', '%' . $filters['search'] . '%')

    //     //         ->orWhereHas('company', function ($q) use ($filters) {
    //     //             $q->where('company_name', 'like', '%' . $filters['search'] . '%');
    //     //         })
    //     //         ->orWhereHas('contract.contract_type', function ($q) use ($filters) {
    //     //             $q->where('contract_type', 'like', '%' . $filters['search'] . '%');
    //     //         })
    //     //         ->orWhereHas('contract.contract_unit', function ($q) use ($filters) {
    //     //             $q->where('business_type', 'like', '%' . $filters['search'] . '%');
    //     //         })
    //     //         ->orWhereHas('tenant', function ($q) use ($filters) {
    //     //             $q->where('tenant_name', 'like', '%' . $filters['search'] . '%')
    //     //                 ->orWhere('tenant_email', 'like', '%' . $filters['search'] . '%')
    //     //                 ->orWhere('tenant_mobile', 'like', '%' . $filters['search'] . '%');
    //     //         })

    //     //         ->orWhereRaw("CAST(contracts.id AS CHAR) LIKE ?", ['%' . $filters['search'] . '%']);
    //     // }
    //     // if (isset($filters['status']) && $filters['status'] !== 'all') {
    //     //     $query->where('agreements.agreement_status', $filters['status']);
    //     // }

    //     // $query->orderBy('agreement_payment_details.id', 'desc');

    //     return $query;
    // }
    public function getQuery(array $filters = []): Builder
    {
        $twoWeeksBeforeEnd = Carbon::today()->addMonths(1)->subWeeks(2)->format('Y-m-d');
        // dd($twoWeeksBeforeEnd);
        $query = AgreementPaymentDetail::query()
            ->with(
                'agreementPayment.installment',
                'paymentMode',
                'bank',
                'agreement',
                'agreement.tenant',
                'agreement.contract',
                'agreement.contract.contract_type',
                'agreement.contract.contract_unit',
                'agreement.contract.contract_unit_details',
                'agreement.contract.property',
                'agreement.agreement_units.contractUnitDetail',
                'agreement.agreement_units.contractSubunitDetail',
                'agreement.agreement_units',
                'clearedReceivables'
            )
            // ->where('id', '>=', 12)
            ->where('is_payment_received', '!=', 1)
            ->where('terminate_status', 0)
            // ->whereDate('payment_date', '>=', Carbon::today())
            ->whereDate('payment_date', '<=', Carbon::today()->addWeeks(2));


        // Get the results
        $results = $query->get();
        // dd($results);




        // $get = $query->get();
        // dd($get);

        if (!empty($filters['search'])) {
            $search = $filters['search'];

            $query->where(function ($q) use ($search) {
                $q->orWhere('payment_amount', 'like', '%' . $search . '%')
                    ->orWhereHas('agreement.contract', function ($q2) use ($search) {
                        $q2->where('project_number', 'like', "%$search%");
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
        // dd($results);

        return $query;
    }
    public function getPaymentDetailById($id)
    {
        return AgreementPaymentDetail::find($id);
    }

    public function updatePaymentDetail(array $data)
    {
        $paymentDetailId = $data['payment_detail_id'];

        $updateData = $data;
        unset($updateData['payment_detail_id'], $updateData['_token']);
        // dd($updateData);

        return AgreementPaymentDetail::where('id', $paymentDetailId)
            ->update($updateData);
    }

    public function updatePayment(array $data)
    {
        $agreementPaymentId = $data['agreement_payment_id'] ?? null;

        if ($agreementPaymentId) {
            $updateData = $data;
            unset($updateData['agreement_payment_id']);
            // dd($data);

            return AgreementPayment::where('id', $agreementPaymentId)
                ->update($updateData);
        }

        return false;
    }
    public function createClearedReceivables(array $data)
    {
        // dd($data);
        return ClearedReceivable::create($data);
    }
    public function bouncedReceivables(array $data)
    {
        $paymentDetailId = $data['payment_detail_id'];

        $updateData = $data;
        unset($updateData['payment_detail_id']);
        // dd($updateData);

        return AgreementPaymentDetail::where('id', $paymentDetailId)
            ->update($updateData);
    }
    public function getReportQuery(array $filters = []): Builder
    {
        $twoWeeksBeforeEnd = Carbon::today()->addMonths(1)->subWeeks(2)->format('Y-m-d');
        // dd($twoWeeksBeforeEnd);


        $query = ClearedReceivable::query()
            ->with([
                'agreementPaymentDetail',
                'agreementPaymentDetail.agreementPayment.installment',
                'agreementPaymentDetail.paymentMode',
                'agreementPaymentDetail.bank',
                'agreementPaymentDetail.agreement',
                'agreementPaymentDetail.agreement.tenant',
                'agreementPaymentDetail.agreement.contract',
                'agreementPaymentDetail.agreement.contract.contract_type',
                'agreementPaymentDetail.agreement.contract.contract_unit',
                'agreementPaymentDetail.agreement.contract.contract_unit_details',
                'agreementPaymentDetail.agreement.contract.property',
                'agreementPaymentDetail.agreement.agreement_units.contractUnitDetail',
                'agreementPaymentDetail.agreement.agreement_units',
                'agreementPaymentDetail.agreement.contract.company',
                'paidBank',
                'paidCompany',
                'paidMode'
            ])
            ->whereHas('agreementPaymentDetail', function ($q) {
                $q->whereIn('is_payment_received', [1, 2])
                    ->where('terminate_status', 0);
            });


        if (!empty($filters['search'])) {
            $search = $filters['search'];
            // dd($search);

            $query->where(function ($q) use ($search) {
                $q->orWhereRaw('CAST(paid_amount AS CHAR) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('CAST(pending_amount AS CHAR) LIKE ?', ["%{$search}%"])
                    // ->orwhere('paid_date', 'like', "%$search%")

                    ->orWhereHas('agreementPaymentDetail.agreement.contract', function ($q2) use ($search) {
                        $q2->where('project_number', 'like', "%$search%");
                    })
                    ->orWhereHas('agreementPaymentDetail.agreement.contract.contract_type', function ($q2) use ($search) {
                        $q2->where('contract_type', 'like', "%$search%");
                    })
                    ->orWhereHas('agreementPaymentDetail.agreement.contract.contract_unit', function ($q2) use ($search) {
                        $q2->where('business_type', 'like', "%$search%");
                    })
                    ->orWhereHas('agreementPaymentDetail.agreement.tenant', function ($q2) use ($search) {
                        $q2->where('tenant_name', 'like', "%$search%")
                            ->orWhere('tenant_email', 'like', "%$search%")
                            ->orWhere('tenant_mobile', 'like', "%$search%");
                    })
                    ->orWhereHas('agreementPaymentDetail.agreementUnit', function ($q2) use ($search) {
                        $q2->whereHas('contractUnitDetail', function ($q3) use ($search) {
                            $q3->where('unit_number', 'like', "%$search%");
                        });
                    })
                    ->orWhereHas('agreementPaymentDetail.agreement.contract.property', function ($q2) use ($search) {
                        $q2->where('property_name', 'like', "%$search%");
                    })
                    ->orWhereHas('agreementPaymentDetail.agreement.contract.contract_type', function ($q2) use ($search) {
                        $q2->where('contract_type', 'like', "%$search%");
                    })


                    ->orWhereHas('agreementPaymentDetail.paymentMode', function ($q2) use ($search) {
                        $q2->where('payment_mode_name', 'like', "%$search%");
                    })
                    ->orWhereHas('paidMode', function ($q2) use ($search) {
                        $q2->where('payment_mode_name', 'like', "%$search%");
                    })
                    ->orWhereHas('paidCompany', function ($q2) use ($search) {
                        $q2->where('company_name', 'like', "%$search%");
                    })
                    ->orWhereHas('agreementPaymentDetail.agreement.contract.company', function ($q2) use ($search) {
                        $q2->where('company_name', 'like', "%$search%");
                    })
                    ->orWhereRaw("CAST(cleared_receivables.id AS CHAR) LIKE ?", ["%$search%"]);
                // if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $search)) {
                //     // dd($search);
                //     $searchDate = Carbon::createFromFormat('d-m-Y', $search)->format('Y-m-d');
                //     dd($searchDate);
                //     $query->Where('paid_date', $searchDate);
                // }
            });
        }
        // if (!empty($filters['company_id'])) {
        //     $query->where('company_id', $filters['company_id']);
        // }


        // Date filter
        if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
            $query->whereBetween('paid_date', [
                Carbon::createFromFormat('d-m-Y', $filters['date_from'])->format('Y-m-d'),
                Carbon::createFromFormat('d-m-Y', $filters['date_to'])->format('Y-m-d'),
            ]);
        }
        // $t = $query->get();
        // dd($t);

        return $query;
    }
}
