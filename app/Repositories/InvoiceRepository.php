<?php

namespace App\Repositories;

use App\Models\Agreement;
use App\Models\AgreementPaymentDetail;
use App\Models\TenantInvoice;
use App\Repositories\Contracts\ContractRepository;
use App\Services\Contracts\UnitService;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class InvoiceRepository
{
    public function __construct(
        protected ContractRepository $contractRepository,
        protected UnitService $contractUnitService


    ) {}



    public function getQuery(array $filters = []): Builder
    {
        // dd($filters);
        $twoWeeksBeforeEnd = Carbon::today()->addMonths(1)->subWeeks(2)->format('Y-m-d');
        // dd($twoWeeksBeforeEnd);

        // Get company IDs where user has finance.payable permission
        $permittedCompanyIds = getUserPermittedCompanyIds(auth()->id(), 'invoice.add');
        // dd($permittedCompanyIds);

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
                'invoice'

            )
            // ->where('id', '>=', 12)
            // ->withSum('clearedReceivables', 'paid_amount')
            // ->where('is_payment_received', '!=', 1)
            // ->where('terminate_status', 0)
            // ->whereDate('payment_date', '>=', Carbon::today())
            // ->whereDate('payment_date', '<=', Carbon::today()->addWeeks(2))
            ->where('is_invoice_added', '=', 0)
            ->whereDate('payment_date', '<=', '2025-12-31')
            ->whereHas('agreement.contract', function ($q) {
                $q->where('contract_type_id', 1)
                    ->whereHas('contract_unit', function ($q2) {
                        $q2->where('business_type', 1);
                    });
            });
        // ->whereHas('agreement.agreement_units', function ($q) {
        //     $q->where('is_rent_bifurcation_added', 1);
        // });

        $query->whereHas('agreement.company', function ($q) use ($permittedCompanyIds) {
            $q->whereIn('company_id', $permittedCompanyIds);
        });

        // $query = AgreementPaymentDetail::query()
        //     ->with([
        //         'paymentMode:id,payment_mode_name',
        //         'bank:id,bank_name',
        //         'agreement:id,tenant_id,contract_id',
        //         'agreement.tenant:id,tenant_name,tenant_email,tenant_mobile',
        //         'agreement.tenant.tenantDocuments:id,tenant_id,document_type,document_number',
        //         'agreement.contract:id,company_id,contract_type_id,property_id,project_number',
        //         'agreement.contract.company:id,company_name',
        //         'agreement.contract.contract_type:id,contract_type',
        //         'agreement.contract.contract_unit:id,business_type,contract_id',
        //         'agreement.contract.property:id,property_name',
        //         'agreement.agreement_units:id,agreement_id,contract_unit_details_id,contract_subunit_details_id,rent_per_month',
        //         'agreement.agreement_units.contractUnitDetail:id,unit_number',
        //         'agreement.agreement_units.contractSubunitDetail:id,subunit_no',
        //         'invoice:id,agreement_payment_detail_id,status,invoice_no,invoice_date,trn_number,month_start,month_end,total_amount',
        //         'invoice.comments:id,tenant_invoice_id',
        //     ])
        //     ->select([
        //         'id',
        //         'agreement_id',
        //         'agreement_unit_id',
        //         'payment_mode_id',
        //         'bank_id',
        //         'cheque_number',
        //         'payment_date',
        //         'payment_amount',
        //         'transaction_type',
        //         'is_invoice_added',
        //     ])
        //     ->where('is_invoice_added', 0)
        //     ->whereHas('agreement.contract', function ($q) {
        //         $q->where('contract_type_id', 1)
        //             ->whereHas('contract_unit', function ($q2) {
        //                 $q2->where('business_type', 1);
        //             });
        //     });


        // $query->whereHas('agreement.contract', function ($q) {
        //     $q->where('contract_type_id', 1)
        //         ->whereHas('contract_unit', function ($q2) {
        //             $q2->where('business_type', 1);
        //         });
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
                            ->orWhereRaw("CONCAT('p-', project_number) LIKE ?", ["%{$search}%"])
                            ->orWhereRaw("CAST(project_number AS CHAR) LIKE ?", ["%{$search}%"]);

                        // Handle "p-1585" → extract "1585"
                        // if (preg_match('/p[\s\-]+(\d+)/i', $search, $matches)) {
                        //     $q2->orWhereRaw("CAST(project_number AS CHAR) LIKE ?", ["%{$matches[1]}%"]);
                        // }
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
        // dd($filters);
        if (isset($filters['status']) && $filters['status'] !== 'all') {
            if ($filters['status'] == 0) {
                $query->whereDoesntHave('invoice');
            } else if ($filters['status'] == 1) {
                $query->whereHas('invoice', function ($q) {
                    $q->where('status', 1);
                });
            } else if ($filters['status'] == 2) {
                $query->whereHas('invoice', function ($q) {
                    $q->where('status', 2);
                });
            } else if ($filters['status'] == 3) {
                $query->whereHas('invoice', function ($q) {
                    $q->where('status', 3);
                });
            }
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
            if (!empty($filters['contract_id'])) {
                $query->whereHas('agreement.contract', function ($q) use ($filters) {
                    $q->where('id', $filters['contract_id']);
                });
            }
        }
        if (!empty($filters['contract_id'])) {
            $query->whereHas('agreement.contract', function ($q) use ($filters) {
                $q->where('id', $filters['contract_id']);
            });
        }
        if (!empty($filters['mode_id'])) {
            $query->where('payment_mode_id', $filters['mode_id']);
        }

        if (!is_null($filters['company_id'])) {
            // dd($filters['company_id']);
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
    public function create($data)
    {
        // dd($data);
        $data['added_by'] = auth()->user()->id;
        $data['status'] = 1;
        return TenantInvoice::create($data);
    }
    public function getDetails($id)
    {
        // dd($id);
        return TenantInvoice::with([
            'agreement.tenant',
            'agreement.contract.property',
            'agreement.contract.locality',
            'agreement.contract.contract_unit',
            'agreementUnit.contractUnitDetail.unit_type',
            'agreementUnit.agreementSubunitRentBifurcation',
            'contract',
            'comments.user',
            'approvedBy',
        ])->findOrFail($id);
    }
    public function update($id, $data)
    {
        $invoice = TenantInvoice::findOrFail($id);

        $data['updated_by'] = auth()->user()->id;

        $invoice->update($data);

        return $invoice;
    }
    public function delete($id)
    {
        $invoice = TenantInvoice::findOrFail($id);
        return $invoice->delete();
    }
    // public function getQueryGenerated(array $filters = []): Builder
    // {
    //     // dd($filters);
    //     $twoWeeksBeforeEnd = Carbon::today()->addMonths(1)->subWeeks(2)->format('Y-m-d');
    //     // dd($twoWeeksBeforeEnd);

    //     // Get company IDs where user has finance.payable permission

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
    //             'invoice'

    //         )
    //         // ->where('id', '>=', 12)
    //         // ->withSum('clearedReceivables', 'paid_amount')
    //         // ->where('is_payment_received', '!=', 1)
    //         // ->where('terminate_status', 0)
    //         // ->whereDate('payment_date', '>=', Carbon::today())
    //         // ->whereDate('payment_date', '<=', Carbon::today()->addWeeks(2))
    //         ->where('is_invoice_added', '=', 1)->whereHas('agreement.contract', function ($q) {
    //             $q->where('contract_type_id', 1)
    //                 ->whereHas('contract_unit', function ($q2) {
    //                     $q2->where('business_type', 1);
    //                 });
    //         });


    //     // $query->whereHas('agreement.contract', function ($q) {
    //     //     $q->where('contract_type_id', 1)
    //     //         ->whereHas('contract_unit', function ($q2) {
    //     //             $q2->where('business_type', 1);
    //     //         });
    //     // });

    //     // Get the results
    //     // $results = $query->get();
    //     // dd($results);




    //     // $get = $query->get();
    //     // dd($get);
    //     // dd($filters);

    //     if (!empty($filters['search'])) {
    //         $search = $filters['search'];
    //         // dd($search);


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
    //                 // ->orWhereHas('agreement.contract', function ($q2) use ($search) {
    //                 //     $q2->whereRaw("CONCAT('P-',project_number) LIKE ?", ['%' . $search . '%']);
    //                 // })

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
    //     // dd($filters);
    //     if (isset($filters['status']) && $filters['status'] !== 'all') {
    //         if ($filters['status'] == 0) {
    //             $query->whereDoesntHave('invoice');
    //         } else if ($filters['status'] == 1) {
    //             $query->whereHas('invoice', function ($q) {
    //                 $q->where('status', 1);
    //             });
    //         } else if ($filters['status'] == 2) {
    //             $query->whereHas('invoice', function ($q) {
    //                 $q->where('status', 2);
    //             });
    //         } else if ($filters['status'] == 3) {
    //             $query->whereHas('invoice', function ($q) {
    //                 $q->where('status', 3);
    //             });
    //         }
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
    // public function getQueryGenerated(array $filters = []): Builder
    // {
    //     $query = AgreementPaymentDetail::query()
    //         ->select([
    //             'id',
    //             'agreement_payment_id',
    //             'agreement_id',
    //             'agreement_unit_id',
    //             'payment_mode_id',
    //             'bank_id',
    //             'payment_amount',
    //             'payment_date',
    //             'is_invoice_added',
    //         ])
    //         ->with([
    //             'agreementPayment:id,installment_id',
    //             'agreementPayment.installment:id,installment_name',

    //             'paymentMode:id,payment_mode_name',
    //             'bank:id,bank_name',

    //             'agreement:id,tenant_id,contract_id',
    //             'agreement.tenant:id,tenant_name,tenant_email,tenant_mobile',
    //             'agreement.contract:id,company_id,contract_type_id,property_id,project_number',
    //             'agreement.contract.company:id,company_name',
    //             'agreement.contract.contract_type:id,contract_type',
    //             'agreement.contract.contract_unit:id,business_type',
    //             'agreement.contract.property:id,property_name',

    //             'agreementUnit:id,agreement_id,contract_unit_details_id,contract_subunit_details_id',
    //             'agreementUnit.contractUnitDetail:id,unit_number,unit_type_id',
    //             'agreementUnit.contractUnitDetail.unit_type:id,unit_type',
    //             'agreementUnit.contractSubunitDetail:id,subunit_no',

    //             'invoice:id,agreement_payment_detail_id,status,invoice_no',
    //         ])
    //         ->where('is_invoice_added', 1)
    //         ->whereHas('agreement.contract', function ($q) {
    //             $q->where('contract_type_id', 1)
    //                 ->whereHas('contract_unit', function ($q2) {
    //                     $q2->where('business_type', 1);
    //                 });
    //         });

    //     if (!empty($filters['search'])) {
    //         $search = trim($filters['search']);

    //         $query->where(function ($q) use ($search) {
    //             $q->where('agreement_payment_details.id', 'like', "%{$search}%")
    //                 ->orWhere('payment_amount', 'like', "%{$search}%")
    //                 ->orWhere('payment_date', 'like', "%{$search}%")
    //                 ->orWhereHas('agreement.contract', function ($q2) use ($search) {
    //                     $q2->where('project_number', 'like', "%{$search}%");
    //                 })
    //                 ->orWhereHas('agreement.contract.contract_type', function ($q2) use ($search) {
    //                     $q2->where('contract_type', 'like', "%{$search}%");
    //                 })
    //                 ->orWhereHas('agreement.tenant', function ($q2) use ($search) {
    //                     $q2->where('tenant_name', 'like', "%{$search}%")
    //                         ->orWhere('tenant_email', 'like', "%{$search}%")
    //                         ->orWhere('tenant_mobile', 'like', "%{$search}%");
    //                 })
    //                 ->orWhereHas('agreementUnit.contractUnitDetail', function ($q2) use ($search) {
    //                     $q2->where('unit_number', 'like', "%{$search}%");
    //                 })
    //                 ->orWhereHas('paymentMode', function ($q2) use ($search) {
    //                     $q2->where('payment_mode_name', 'like', "%{$search}%");
    //                 })
    //                 ->orWhereHas('agreement.contract.company', function ($q2) use ($search) {
    //                     $q2->where('company_name', 'like', "%{$search}%");
    //                 })
    //                 ->orWhereHas('agreement.contract.property', function ($q2) use ($search) {
    //                     $q2->where('property_name', 'like', "%{$search}%");
    //                 });
    //         });
    //     }

    //     if (isset($filters['status']) && $filters['status'] !== 'all') {
    //         if ($filters['status'] == 0) {
    //             $query->whereDoesntHave('invoice');
    //         } else {
    //             $query->whereHas('invoice', function ($q) use ($filters) {
    //                 $q->where('status', $filters['status']);
    //             });
    //         }
    //     }

    //     if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
    //         $query->whereBetween('payment_date', [
    //             Carbon::createFromFormat('d-m-Y', $filters['date_from'])->format('Y-m-d'),
    //             Carbon::createFromFormat('d-m-Y', $filters['date_to'])->format('Y-m-d'),
    //         ]);
    //     }

    //     if (!empty($filters['unit_id'])) {
    //         $query->whereHas('agreementUnit.contractUnitDetail', function ($q) use ($filters) {
    //             $q->where('id', $filters['unit_id']);
    //         });
    //     } elseif (!empty($filters['property_id'])) {
    //         $query->whereHas('agreement.contract.property', function ($q) use ($filters) {
    //             $q->where('id', $filters['property_id']);
    //         });
    //     }

    //     if (!empty($filters['mode_id'])) {
    //         $query->where('payment_mode_id', $filters['mode_id']);
    //     }

    //     if (!empty($filters['company_id'])) {
    //         $query->whereHas('agreement.contract', function ($q) use ($filters) {
    //             $q->where('company_id', $filters['company_id']);
    //         });
    //     }

    //     if (!empty($filters['tenant_id'])) {
    //         $query->whereHas('agreement.tenant', function ($q) use ($filters) {
    //             $q->where('id', $filters['tenant_id']);
    //         });
    //     }

    //     return $query->orderByDesc('agreement_payment_details.id');
    // }

    public function getQueryGenerated(array $filters = []): Builder
    {

        // Get company IDs where user has finance.payable permission
        $permittedCompanyIds = getUserPermittedCompanyIds(auth()->id(), 'invoice.view');

        $query = TenantInvoice::query()
            ->with([
                'agreementPaymentDetail.paymentMode:id,payment_mode_name',
                'agreementPaymentDetail.bank:id,bank_name',
                // 'agreementPaymentDetail.agreement:id,company_id',
                'agreementPaymentDetail.agreement.company:id,company_name',
                'agreementPaymentDetail.agreement:id,tenant_id,contract_id',
                'agreementPaymentDetail.agreement.tenant:id,tenant_name,tenant_email,tenant_mobile',
                'agreementPaymentDetail.agreement.tenant.tenantDocuments:id,tenant_id,document_type,document_number',
                'agreementPaymentDetail.agreement.contract:id,company_id,contract_type_id,property_id,project_number',
                'agreementPaymentDetail.agreement.contract.company:id,company_name',
                'agreementPaymentDetail.agreement.contract.contract_type:id,contract_type',
                'agreementPaymentDetail.agreement.contract.contract_unit:id,contract_id,business_type',
                'agreementPaymentDetail.agreement.contract.property:id,property_name',
                'agreementPaymentDetail.agreement.agreement_units:id,agreement_id,contract_unit_details_id,contract_subunit_details_id,rent_per_month',
                'agreementPaymentDetail.agreement.agreement_units.contractUnitDetail:id,unit_number',
                'agreementPaymentDetail.agreement.agreement_units.contractSubunitDetail:id,subunit_no',
            ])
            ->select([
                'id',
                'agreement_payment_detail_id',
                'status',
                'invoice_no',
                'invoice_date',
                // 'trn_number',
                'month_start',
                'month_end',
                'total_amount',
            ])
            ->where('status', 2)
            ->whereHas('agreementPaymentDetail', function ($q) {
                $q->where('is_invoice_added', 1);
            })
            ->whereHas('agreementPaymentDetail.agreement.contract', function ($q) {
                $q->where('contract_type_id', 1)
                    ->whereHas('contract_unit', function ($q2) {
                        $q2->where('business_type', 1);
                    });
            });
        $query->whereHas('agreementPaymentDetail.agreement.company', function ($q) use ($permittedCompanyIds) {
            $q->whereIn('company_id', $permittedCompanyIds);
        });

        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];

            $query->where(function ($q) use ($search) {
                $q->orWhere('invoice_no', 'like', '%' . $search . '%')
                    ->orWhere('total_amount', 'like', '%' . $search . '%')
                    ->orWhereHas('agreementPaymentDetail', function ($q2) use ($search) {
                        $q2->where(function ($q3) use ($search) {
                            $q3->where('payment_amount', 'like', '%' . $search . '%')
                                ->orWhere('payment_date', 'like', '%' . $search . '%');
                        });
                    })
                    ->orWhereHas('agreementPaymentDetail.agreement.contract', function ($q2) use ($search) {
                        $q2->whereRaw("CONCAT('P - ', project_number) LIKE ?", ["%{$search}%"])
                            ->orWhereRaw("CONCAT('P-', project_number) LIKE ?", ["%{$search}%"])
                            ->orWhereRaw("CAST(project_number AS CHAR) LIKE ?", ["%{$search}%"]);
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
                    ->orWhereHas('agreementPaymentDetail.agreementUnit.contractUnitDetail', function ($q2) use ($search) {
                        $q2->where('unit_number', 'like', "%$search%");
                    })
                    ->orWhereHas('agreementPaymentDetail.paymentMode', function ($q2) use ($search) {
                        $q2->where('payment_mode_name', 'like', "%$search%");
                    })
                    ->orWhereHas('agreementPaymentDetail.agreement.contract.company', function ($q2) use ($search) {
                        $q2->where('company_name', 'like', "%$search%");
                    })
                    ->orWhereHas('agreementPaymentDetail.agreement.contract.property', function ($q2) use ($search) {
                        $q2->where('property_name', 'like', "%$search%");
                    })
                    ->orWhereRaw("CAST(tenant_invoices.id AS CHAR) LIKE ?", ["%$search%"]);
            });
        }

        // Status filter
        if (isset($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
            // ✅ Since we are ON TenantInvoice, status is a direct column — no whereHas needed
        }

        // Date filter
        if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
            $query->whereBetween('invoice_date', [
                Carbon::createFromFormat('d-m-Y', $filters['date_from'])->format('Y-m-d'),
                Carbon::createFromFormat('d-m-Y', $filters['date_to'])->format('Y-m-d'),
            ]);
        }

        // Unit filter
        if (!empty($filters['unit_id'])) {
            $query->whereHas('agreementPaymentDetail.agreementUnit', function ($q) use ($filters) {
                $q->whereHas('contractUnitDetail', function ($q2) use ($filters) {
                    $q2->where('id', $filters['unit_id']);
                });
                if (!empty($filters['tenant_id'])) {
                    $q->whereHas('agreement.tenant', function ($q3) use ($filters) {
                        $q3->where('id', $filters['tenant_id']);
                    });
                }
            });
        } elseif (!empty($filters['property_id'])) {
            $query->whereHas('agreementPaymentDetail.agreement.contract.property', function ($q) use ($filters) {
                $q->where('id', $filters['property_id']);
            });
        }

        // Contract filter
        if (!empty($filters['contract_id'])) {
            $query->whereHas('agreementPaymentDetail.agreement.contract', function ($q) use ($filters) {
                $q->where('id', $filters['contract_id']);
            });
        }

        // Payment mode filter
        if (!empty($filters['mode_id'])) {
            $query->whereHas('agreementPaymentDetail', function ($q) use ($filters) {
                $q->where('payment_mode_id', $filters['mode_id']);
            });
        }

        // Company filter
        if (!is_null($filters['company_id'])) {
            $query->whereHas('agreementPaymentDetail.agreement.contract', function ($q) use ($filters) {
                $q->where('company_id', $filters['company_id']);
            });
        }

        // Tenant filter
        if (empty($filters['unit_id']) && !empty($filters['tenant_id'])) {
            $query->whereHas('agreementPaymentDetail.agreement.tenant', function ($q) use ($filters) {
                $q->where('id', $filters['tenant_id']);
            });
        }

        return $query;
    }
}
