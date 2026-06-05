<?php

namespace App\Services;

use App\Models\AgreementPaymentDetail;
use App\Models\TenantInvoice;
use App\Models\TenantInvoiceApprovalComments;
use App\Repositories\Agreement\AgreementDocRepository;
use App\Repositories\Agreement\AgreementPaymentDetailRepository;
use App\Repositories\Agreement\AgreementPaymentRepository;
use App\Repositories\Agreement\AgreementRepository;
use App\Repositories\Agreement\AgreementTenantRepository;
use App\Repositories\Agreement\AgreementUnitRepository;
use App\Repositories\InvoiceRepository;
use App\Services\Agreement\AgreementDocumentService;
use App\Services\Agreement\AgreementPaymentDetailService;
use App\Services\Agreement\AgreementPaymentService;
use App\Services\Agreement\AgreementTenantService;
use App\Services\Agreement\AgreementUnitService;
use App\Services\Contracts\ContractService;
use App\Services\Contracts\SubUnitDetailService;
use App\Services\Contracts\UnitService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

class InvoiceService
{
    public function __construct(
        protected InvoiceRepository $invoiceRepository,
        protected AgreementDocRepository $agreementDocRepository,
        protected AgreementTenantRepository $agreementTenantRepository,
        protected AgreementPaymentRepository $agreementPaymentRepository,
        protected AgreementPaymentDetailRepository $agreementPaymentDetailRepository,
        protected AgreementUnitRepository $agreementUnitRepository,
        protected AgreementTenantService $agreementTenantService,
        protected AgreementPaymentDetailService $agreementPaymentDetailService,
        protected AgreementPaymentService $agreementPaymentService,
        protected AgreementUnitService $agreementUnitService,
        protected AgreementDocumentService $agreementDocumentService,
        protected SubUnitDetailService $subUnitDetailserv,
        protected ContractService $contractService,
        protected UnitService $contractUnitService

    ) {}

    public function getDataTable(array $filters = [])
    {
        $query = $this->invoiceRepository->getQuery($filters);
        // dd($query);

        $columns = [
            ['data' => 'DT_RowIndex', 'name' => 'agreement_payment_details.id'],
            ['data' => 'project_number', 'name' => 'project_number'],
            ['data' => 'company_name', 'name' => 'company_name'],
            ['data' => 'tenant_name', 'name' => 'tenant_name'],
            ['data' => 'property_name', 'name' => 'property_name'],
            ['data' => 'unit_number', 'name' => 'unit_number'],
            ['data' => 'subunit_no', 'name' => 'subunit_no'],
            ['data' => 'tenant_details', 'name' => 'tenant_details'],
            ['data' => 'payment_date', 'name' => 'payment_date'],
            ['data' => 'payment_mode_name', 'name' => 'payment_mode_name'],
            ['data' => 'payment_amount', 'name' => 'payment_amount'],
            ['data' => 'installment_name', 'name' => 'installment_name'],
            ['data' => 'status', 'name' => 'status'],
            // ['data' => 'action', 'name' => 'action', 'orderable' => true, 'searchable' => true],
        ];
        // dd("test");

        //  Fresh lightweight query to get unit IDs only
        $unitIds = AgreementPaymentDetail::query()
            ->where('is_invoice_added', 0)
            ->whereHas('agreement.contract', function ($q) {
                $q->where('contract_type_id', 1)
                    ->whereHas('contract_unit', function ($q2) {
                        $q2->where('business_type', 1);
                    });
            })
            ->pluck('agreement_unit_id')
            ->filter()
            ->unique();

        //  Now preload all installments in one query
        $installmentCache = AgreementPaymentDetail::whereIn('agreement_unit_id', $unitIds)
            ->select('id', 'agreement_unit_id', 'payment_date')
            ->orderBy('payment_date')
            ->get()
            ->groupBy('agreement_unit_id');

        return datatables()

            ->of($query)
            ->addIndexColumn()
            ->addColumn('checkbox', function ($row) {
                return '
                <div class="icheck-primary d-inline">
                    <input type="checkbox"
                        class="groupCheckbox"
                        name="installment_id[]"
                        id="ichek' . $row->id . '"
                        value="' . $row->id . '">
                    <label for="ichek' . $row->id . '"></label>
                </div>';
            })
            ->addColumn('project_number', function ($row) {
                // dd($row);
                $number = 'P - ' . $row->agreement?->contract?->project_number ?? '-';
                $type = $row->agreement?->contract?->contract_type->contract_type ?? '-';
                $b_type_id = $row->agreement?->contract?->contract_unit->business_type;
                $b_type = $row->agreement?->contract?->contract_unit->business_type();

                // return "<strong class=''>{$number}</strong><p class='mb-0'><span>{$type}</span></p>
                // </p>";
                $badgeClass = '';
                if ($row->agreement?->contract?->contract_type_id == 1) {
                    $badgeClass = 'badge badge-df text-dark';
                } elseif ($row->agreement?->contract?->contract_type_id == 2) {
                    $badgeClass = 'badge badge-ff text-dark';
                } else {
                    $badgeClass = 'badge badge-secondary';
                }
                // Business type color
                $businessClass = ($b_type_id == 1) ? 'text-olive' : 'text-cyan';

                return "<strong>{$number}</strong>
            <p class='mb-0'>
                <span class='{$badgeClass}'>{$type}</span>
            </p>
           <strong class='{$businessClass}'>
            {$b_type}
        </strong>";
            })
            ->addColumn('company_name', fn($row) => $row->agreement?->contract?->company->company_name ?? '-')
            ->addColumn('tenant_name', function ($row) {

                $name = $row->agreement?->tenant->tenant_name ?? '-';
                $email = $row->agreement?->tenant->tenant_email ?? '-';
                $phone = $row->agreement?->tenant->tenant_mobile ?? '-';

                return "<strong class='text-capitalize'>{$name}</strong><p class='mb-0 text-primary'>{$email}</p><p class='text-muted small'>
                    <i class='fa fa-phone-alt text-danger'></i> <span class='font-weight-bold'>{$phone}</span>
                </p>";
            })
            ->addColumn('property_name', fn($row) => $row->agreement?->contract?->property->property_name ?? '-')
            ->addColumn('unit_number', function ($row) {
                // Find the agreement unit that matches this payment detail
                $unit = $row->agreement?->agreement_units->firstWhere('id', $row->agreement_unit_id);

                return $unit && $unit->contractUnitDetail
                    ? $unit->contractUnitDetail->unit_number
                    : '-';
            })
            ->addColumn('subunit_no', function ($row) {
                // Find the agreement unit that matches this payment detail
                $unit = $row->agreement?->agreement_units->firstWhere('id', $row->agreement_unit_id);

                return $unit && $unit->contractSubunitDetail
                    ? $unit->contractSubunitDetail->subunit_no
                    : '-';
            })
            ->addColumn('payment_date', function ($row) {
                if (!$row->payment_date) {
                    return '-';
                }

                return Carbon::parse($row->payment_date)->format('d-m-Y');
            })

            // ->addColumn('cheque_number', fn($row) => $row->cheque_number ?? '-')
            ->addColumn('payment_mode_name', function ($row) {
                $text = $row->paymentMode ? $row->paymentMode->payment_mode_name : '';

                if (!empty($row->bank_id) && $row->bank) {
                    $text .= ' - ' . ucfirst($row->bank->bank_name);
                }

                if (!empty($row->cheque_number)) {
                    $text .= ' - ' . ucfirst($row->cheque_number);
                }

                return $text;
            })

            ->addColumn('payment_amount', function ($row) {
                return $row->payment_amount;
            })
            // ->addColumn('installment_name', function ($row) {
            //     // dd($row->transaction_type);

            //     if (empty($row->agreement_unit_id)) {
            //         return match ((int) $row->transaction_type) {
            //             2 => '<span class="badge bg-danger">Termination Payback</span>',
            //             1 => '<span class="badge bg-success">Termination Receive</span>',
            //             default => '<span class="badge bg-secondary">-</span>',
            //         };
            //     }
            //     $agreementUnitId = $row->agreement_unit_id;

            //     $installments = AgreementPaymentDetail::where('agreement_unit_id', $agreementUnitId)
            //         ->orderBy('payment_date')
            //         ->get();
            //     // dd($installments);

            //     $current = 0;
            //     $total = $installments->count();

            //     foreach ($installments as $index => $installment) {
            //         if ($installment->payment_date == $row->payment_date) {
            //             $current = $index + 1;
            //             break;
            //         }
            //     }

            //     return "{$current}/{$total}";
            // })
            ->addColumn('installment_name', function ($row) use ($installmentCache) {
                if (empty($row->agreement_unit_id)) {
                    return match ((int) $row->transaction_type) {
                        2 => '<span class="badge bg-danger">Termination Payback</span>',
                        1 => '<span class="badge bg-success">Termination Receive</span>',
                        default => '<span class="badge bg-secondary">-</span>',
                    };
                }

                $installments = $installmentCache->get($row->agreement_unit_id, collect());
                $total = $installments->count();
                $current = $installments->search(fn($i) => $i->payment_date == $row->payment_date) + 1;

                return "{$current}/{$total}";
            })
            ->addColumn('status', function ($row) {
                // If any payment has bounced, show Bounced
                if (!$row->invoice) {
                    return '<span class="badge bg-warning">Pending</span>';
                } elseif ($row->invoice && $row->invoice->status == 1) {
                    return '<span class="badge bg-info">Generated</span>';
                } elseif ($row->invoice && $row->invoice->status == 2) {
                    return '<span class="badge bg-success">Approved</span>';
                } elseif ($row->invoice && $row->invoice->status == 3) {
                    $comment = '';
                    if ($row->invoice->status == 3) {
                        $comment = '<i class="far fa-comments loadComments"
                    data-id="' . $row->invoice->id . '"
                    style="cursor:pointer;"></i>';; //data-toggle="modal" data-target="#modal-hold-comment"
                    }
                    $badge = '<span class="badge bg-danger">On Hold</span>';
                    return $comment ? $badge . ' ' . $comment : $badge;
                } else {
                    return '<span class="badge bg-secondary">-</span>';
                }
            })

            ->addColumn('action', function ($row) {

                $action = '';

                // $viewUrl    = route('invoices.show', $row->invoice?->id);
                $editUrl    = route('invoices.edit', $row->id);
                $generateUrl = route('invoices.generate', $row->id); // example

                /* VIEW */
                if (auth()->user()->hasAnyPermission(['invoice.view'], $row->agreement?->company->id) && $row->invoice) {
                    $viewUrl = route('invoices.show', $row->invoice->id);

                    $action .= '<a href="' . $viewUrl . '" class="btn btn-primary btn-sm mr-1" title="View">
                        <i class="fas fa-eye"></i>
                    </a>';
                }
                // Find the agreement unit that matches this payment detail
                $unit = $row->agreement?->agreement_units->firstWhere('id', $row->agreement_unit_id);
                $trn_number = $row->agreement?->tenant?->tenantDocuments
                    ?->where('document_type', 3)
                    ->first()
                    ?->document_number ?? '-';

                if (auth()->user()->hasAnyPermission(['invoice.add'], $row->agreement?->company->id) && !$row->invoice) {
                    $action .= '<a href="javascript:void(0)"
                    class="btn btn-maroon btn-sm mr-1 open-generate-modal"
                    data-id="' . $row->id . '"
                    data-tenant="' . $row->agreement->tenant->tenant_name . '"
                    data-unit="' . ($unit->contractUnitDetail->unit_number) . '"
                    data-trn="' . $trn_number . '"
                    data-agreement-id="' . $row->agreement_id . '"
                    data-tenant-id="' . $row->agreement->tenant_id . '"
                    data-contract-id="' . $row->agreement->contract_id . '"
                    data-agreement-unit-id="' . $row->agreement_unit_id . '"
                    data-contract-unit-id="' . $unit->contractUnitDetail->id . '"
                    data-total-amount = "' . $unit->rent_per_month . '"
                    data-payment-date = "' . $row->payment_date . '"
                    title="Generate Invoice">
                    <i class="fas fa-file-invoice"></i>
                </a>';
                }


                if (auth()->user()->hasAnyPermission(['invoice.approve'], $row->agreement?->company->id) && $row->invoice && $row->invoice->status != 2) {
                    $approveUrl = route('invoices.approve', $row->invoice?->id);   // example

                    $action .= '<a href="javascript:void(0)"
                        class="btn btn-success btn-sm mr-1 open-approve-modal"
                        data-id="' . $row->invoice->id . '"
                        data-invoice-number="' . $row->invoice->invoice_no . '"
                        data-tenant="' . $row->agreement->tenant->tenant_name . '"
                        data-approve-url="' . $approveUrl . '"
                        title="Approve">
                        <i class="fas fa-check"></i>
                    </a>';
                }


                if (auth()->user()->hasAnyPermission(['invoice.edit'], $row->agreement?->company->id) && $row->invoice && $row->invoice->status != 2) {
                    $action .= '<a href="javascript:void(0)"
                        class="btn btn-info btn-sm mr-1 open-edit-modal"
                        data-id="' . $row->invoice->id . '"
                        data-tenant="' . $row->agreement->tenant->tenant_name . '"
                        data-unit="' . ($unit->contractUnitDetail->unit_number) . '"
                        data-tenant-id="' . $row->agreement->tenant_id . '"
                        data-date="' . \Carbon\Carbon::parse($row->invoice->invoice_date)->format('d-m-Y') . '"
                        data-trn="' . $trn_number  . '"
                        data-start="' . $row->invoice->month_start . '"
                        data-end="' . $row->invoice->month_end . '"
                        data-amount="' . $row->invoice->total_amount . '"
                        title="Edit">
                        <i class="fas fa-pencil-alt"></i>
                    </a>';
                }
                if (auth()->user()->hasAnyPermission(['invoice.admin-view'], $row->agreement?->company->id) && $row->invoice && $row->invoice->comments->count() > 0) {
                    $action .= '<a href="' . route('invoices.admin-view', $row->invoice->id) . '"
                        class="btn btn-secondary btn-sm mr-1"
                        title="Comments">
                        <i class="fas fa-comments"></i>
                    </a>';
                }


                /* DELETE */
                if (auth()->user()->hasAnyPermission(['invoice.delete'], $row->agreement?->company->id) && $row->invoice && $row->invoice->status == 1) {

                    $action .= '<a href="javascript:void(0)"
                        class="btn btn-danger btn-sm mr-1"
                        onclick="deleteConf(' . $row->invoice->id . ')"
                        title="Delete">
                        <i class="fas fa-trash"></i>
                    </a>';
                }

                return $action ?: '-';
            })


            ->rawColumns(['checkbox', 'tenant_name', 'action', 'project_number', 'business_type', 'status', 'installment_name'])
            // ->rawColumns(['action'])
            ->with(['columns' => $columns])
            ->toJson();
    }
    public function create($data)
    {
        return DB::transaction(function () use ($data) {

            $data['invoice_date'] = parseDate($data['invoice_date']);
            $data['month_start']  = parseDate($data['month_start']);
            $data['month_end']    = parseDate($data['month_end']);
            $data['invoice_no'] = $this->generateInvoiceNumber();

            $tenantId   = $data['tenant_id'];
            $newTrn     = $data['trn_number'];

            // Get existing TRN from document type 3
            $existingTrn = $this->agreementTenantService
                ->getDocumentNumberByTenantAndType($tenantId, 3);

            // Update only if changed
            if ($existingTrn !== $newTrn) {
                $this->agreementTenantService->updateDocumentNumberByTenantAndType(
                    $tenantId,
                    3,
                    $newTrn
                );
            }
            // dd($data);

            $invoice = $this->invoiceRepository->create($data);

            return $invoice;
        });
    }
    private function generateInvoiceNumber(): string
    {
        $last = TenantInvoice::orderBy('id', 'desc')->first();

        if ($last && preg_match('/INV-(\d+)/', $last->invoice_no, $matches)) {
            $nextNumber = (int) $matches[1] + 1;
        } else {
            $nextNumber = 1;
        }

        return 'INV-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
    public function getDetails($id)
    {
        return $this->invoiceRepository->getDetails($id);
    }


    public function approve($request, $id)
    {
        try {

            return DB::transaction(function () use ($request, $id) {

                $invoice = TenantInvoice::findOrFail($id);

                // if ($invoice->status != 1) {
                //     return [
                //         'success' => false,
                //         'message' => 'Invoice is not in a pending state.',
                //     ];
                // }

                $status = $request['status'] ?? null;

                //  Update invoice
                if ($status == 2) {
                    $invoice->update([
                        'status' => $status,
                        'approved_by' => auth()->id(),
                        'approved_date' => now(),
                        // 'approved_at' => now(),
                    ]);
                } else {
                    $invoice->update([
                        'status' => $status,
                    ]);
                }

                //  Save comment
                $comment = $request['comment'] ?? null;

                if (!empty($comment)) {
                    TenantInvoiceApprovalComments::create([
                        'tenant_invoice_id' => $invoice->id,
                        'added_by'          => auth()->id(),
                        'comment'           => $comment,
                    ]);
                }

                // Update agreement payment detail ONLY when approved
                if ($status == 2) {

                    $agreementPaymentDetail = AgreementPaymentDetail::where(
                        'id',
                        $invoice->agreement_payment_detail_id
                    )->first();

                    if ($agreementPaymentDetail) {
                        $agreementPaymentDetail->update([
                            'is_invoice_added' => 1
                        ]);
                    }
                }

                return [
                    'success' => true,
                    'message' => $status == 2
                        ? 'Invoice #' . $invoice->invoice_no . ' approved successfully.'
                        : 'Invoice #' . $invoice->invoice_no . ' moved to On Hold.',
                ];
            });
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ];
        }
    }
    public function update($id, $data)
    {
        // dd($data);
        return DB::transaction(function () use ($data, $id) {

            // Format dates
            $data['invoice_date'] = parseDate($data['invoice_date']);
            $data['month_start']  = parseDate($data['month_start']);
            $data['month_end']    = parseDate($data['month_end']);


            $tenantId = $data['tenant_id'];
            $newTrn   = $data['trn_number'];

            // Get existing TRN
            $existingTrn = $this->agreementTenantService
                ->getDocumentNumberByTenantAndType($tenantId, 3);

            // Update TRN only if changed
            if ($existingTrn !== $newTrn) {
                $this->agreementTenantService->updateDocumentNumberByTenantAndType(
                    $tenantId,
                    3,
                    $newTrn
                );
            }

            //  UPDATE instead of create
            $invoice = $this->invoiceRepository->update($id, $data);

            return $invoice;
        });
    }
    public function delete($id)
    {
        return $this->invoiceRepository->delete($id);
    }
    public function addComment($data, $id)
    {
        try {
            $comment = $data['comment'] ?? null;

            $commentModel = TenantInvoiceApprovalComments::create([
                'tenant_invoice_id' => $id,
                'added_by' => auth()->id(),
                'comment' => $comment,
            ]);

            return [
                'success' => true,
                'message' => 'Comment added successfully',
                'data' => $commentModel
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    // public function getGeneratedInvoices(array $filters = [])
    // {
    //     $query = $this->invoiceRepository->getQueryGenerated($filters);
    //     // dd($query);

    //     $columns = [
    //         ['data' => 'DT_RowIndex', 'name' => 'agreement_payment_details.id'],
    //         ['data' => 'project_number', 'name' => 'project_number'],
    //         ['data' => 'company_name', 'name' => 'company_name'],
    //         ['data' => 'tenant_name', 'name' => 'tenant_name'],
    //         ['data' => 'property_name', 'name' => 'property_name'],
    //         ['data' => 'unit_number', 'name' => 'unit_number'],
    //         ['data' => 'subunit_no', 'name' => 'subunit_no'],
    //         ['data' => 'tenant_details', 'name' => 'tenant_details'],
    //         ['data' => 'payment_date', 'name' => 'payment_date'],
    //         ['data' => 'payment_mode_name', 'name' => 'payment_mode_name'],
    //         ['data' => 'payment_amount', 'name' => 'payment_amount'],
    //         ['data' => 'installment_name', 'name' => 'installment_name'],
    //         ['data' => 'status', 'name' => 'status'],
    //         // ['data' => 'action', 'name' => 'action', 'orderable' => true, 'searchable' => true],
    //     ];
    //     // dd("test");

    //     //  Fresh lightweight query to get unit IDs only
    //     $unitIds = AgreementPaymentDetail::query()
    //         ->where('is_invoice_added', 1)
    //         ->whereHas('agreement.contract', function ($q) {
    //             $q->where('contract_type_id', 1)
    //                 ->whereHas('contract_unit', function ($q2) {
    //                     $q2->where('business_type', 1);
    //                 });
    //         })
    //         ->pluck('agreement_unit_id')
    //         ->filter()
    //         ->unique();

    //     //  Now preload all installments in one query
    //     $installmentCache = AgreementPaymentDetail::whereIn('agreement_unit_id', $unitIds)
    //         ->select('id', 'agreement_unit_id', 'payment_date')
    //         ->orderBy('payment_date')
    //         ->get()
    //         ->groupBy('agreement_unit_id');

    //     return datatables()

    //         ->of($query)
    //         ->addIndexColumn()
    //         ->addColumn('checkbox', function ($row) {
    //             return '
    //             <div class="icheck-primary d-inline">
    //                 <input type="checkbox"
    //                     class="groupCheckbox"
    //                     name="installment_id[]"
    //                     id="ichek' . $row->id . '"
    //                     value="' . $row->id . '">
    //                 <label for="ichek' . $row->id . '"></label>
    //             </div>';
    //         })
    //         ->addColumn('project_number', function ($row) {
    //             // dd($row);
    //             $number = 'P - ' . $row->agreement?->contract?->project_number ?? '-';
    //             $type = $row->agreement?->contract?->contract_type->contract_type ?? '-';
    //             $b_type_id = $row->agreement?->contract?->contract_unit->business_type;
    //             $b_type = $row->agreement?->contract?->contract_unit->business_type();

    //             // return "<strong class=''>{$number}</strong><p class='mb-0'><span>{$type}</span></p>
    //             // </p>";
    //             $badgeClass = '';
    //             if ($row->agreement?->contract?->contract_type_id == 1) {
    //                 $badgeClass = 'badge badge-df text-dark';
    //             } elseif ($row->agreement?->contract?->contract_type_id == 2) {
    //                 $badgeClass = 'badge badge-ff text-dark';
    //             } else {
    //                 $badgeClass = 'badge badge-secondary';
    //             }
    //             // Business type color
    //             $businessClass = ($b_type_id == 1) ? 'text-olive' : 'text-cyan';

    //             return "<strong>{$number}</strong>
    //         <p class='mb-0'>
    //             <span class='{$badgeClass}'>{$type}</span>
    //         </p>
    //        <strong class='{$businessClass}'>
    //         {$b_type}
    //     </strong>";
    //         })
    //         ->addColumn('company_name', fn($row) => $row->agreement?->contract?->company->company_name ?? '-')
    //         ->addColumn('tenant_name', function ($row) {

    //             $name = $row->agreement?->tenant->tenant_name ?? '-';
    //             $email = $row->agreement?->tenant->tenant_email ?? '-';
    //             $phone = $row->agreement?->tenant->tenant_mobile ?? '-';

    //             return "<strong class='text-capitalize'>{$name}</strong><p class='mb-0 text-primary'>{$email}</p><p class='text-muted small'>
    //                 <i class='fa fa-phone-alt text-danger'></i> <span class='font-weight-bold'>{$phone}</span>
    //             </p>";
    //         })
    //         ->addColumn('property_name', fn($row) => $row->agreement?->contract?->property->property_name ?? '-')
    //         ->addColumn('unit_number', function ($row) {
    //             // Find the agreement unit that matches this payment detail
    //             $unit = $row->agreement?->agreement_units->firstWhere('id', $row->agreement_unit_id);

    //             return $unit && $unit->contractUnitDetail
    //                 ? $unit->contractUnitDetail->unit_number
    //                 : '-';
    //         })
    //         ->addColumn('subunit_no', function ($row) {
    //             // Find the agreement unit that matches this payment detail
    //             $unit = $row->agreement?->agreement_units->firstWhere('id', $row->agreement_unit_id);

    //             return $unit && $unit->contractSubunitDetail
    //                 ? $unit->contractSubunitDetail->subunit_no
    //                 : '-';
    //         })
    //         ->addColumn('payment_date', function ($row) {
    //             if (!$row->payment_date) {
    //                 return '-';
    //             }

    //             return Carbon::parse($row->payment_date)->format('d-m-Y');
    //         })

    //         // ->addColumn('cheque_number', fn($row) => $row->cheque_number ?? '-')
    //         ->addColumn('payment_mode_name', function ($row) {
    //             $text = $row->paymentMode ? $row->paymentMode->payment_mode_name : '';

    //             if (!empty($row->bank_id) && $row->bank) {
    //                 $text .= ' - ' . ucfirst($row->bank->bank_name);
    //             }

    //             if (!empty($row->cheque_number)) {
    //                 $text .= ' - ' . ucfirst($row->cheque_number);
    //             }

    //             return $text;
    //         })

    //         ->addColumn('payment_amount', function ($row) {
    //             return $row->payment_amount;
    //         })
    //         // ->addColumn('installment_name', function ($row) {
    //         //     // dd($row->transaction_type);

    //         //     if (empty($row->agreement_unit_id)) {
    //         //         return match ((int) $row->transaction_type) {
    //         //             2 => '<span class="badge bg-danger">Termination Payback</span>',
    //         //             1 => '<span class="badge bg-success">Termination Receive</span>',
    //         //             default => '<span class="badge bg-secondary">-</span>',
    //         //         };
    //         //     }
    //         //     $agreementUnitId = $row->agreement_unit_id;

    //         //     $installments = AgreementPaymentDetail::where('agreement_unit_id', $agreementUnitId)
    //         //         ->orderBy('payment_date')
    //         //         ->get();
    //         //     // dd($installments);

    //         //     $current = 0;
    //         //     $total = $installments->count();

    //         //     foreach ($installments as $index => $installment) {
    //         //         if ($installment->payment_date == $row->payment_date) {
    //         //             $current = $index + 1;
    //         //             break;
    //         //         }
    //         //     }

    //         //     return "{$current}/{$total}";
    //         // })
    //         ->addColumn('installment_name', function ($row) use ($installmentCache) {
    //             if (empty($row->agreement_unit_id)) {
    //                 return match ((int) $row->transaction_type) {
    //                     2 => '<span class="badge bg-danger">Termination Payback</span>',
    //                     1 => '<span class="badge bg-success">Termination Receive</span>',
    //                     default => '<span class="badge bg-secondary">-</span>',
    //                 };
    //             }

    //             $installments = $installmentCache->get($row->agreement_unit_id, collect());
    //             $total = $installments->count();
    //             $current = $installments->search(fn($i) => $i->payment_date == $row->payment_date) + 1;

    //             return "{$current}/{$total}";
    //         })
    //         ->addColumn('status', function ($row) {
    //             // If any payment has bounced, show Bounced
    //             if (!$row->invoice) {
    //                 return '<span class="badge bg-warning">Pending</span>';
    //             } elseif ($row->invoice && $row->invoice->status == 1) {
    //                 return '<span class="badge bg-info">Generated</span>';
    //             } elseif ($row->invoice && $row->invoice->status == 2) {
    //                 return '<span class="badge bg-success">Approved</span>';
    //             } elseif ($row->invoice && $row->invoice->status == 3) {
    //                 $comment = '';
    //                 if ($row->invoice->status == 3) {
    //                     $comment = '<i class="far fa-comments loadComments"
    //                 data-id="' . $row->invoice->id . '"
    //                 style="cursor:pointer;"></i>';; //data-toggle="modal" data-target="#modal-hold-comment"
    //                 }
    //                 $badge = '<span class="badge bg-danger">On Hold</span>';
    //                 return $comment ? $badge . ' ' . $comment : $badge;
    //             } else {
    //                 return '<span class="badge bg-secondary">-</span>';
    //             }
    //         })

    //         ->addColumn('action', function ($row) {

    //             $action = '';

    //             // $viewUrl    = route('invoices.show', $row->invoice?->id);
    //             $editUrl    = route('invoices.edit', $row->id);
    //             $generateUrl = route('invoices.generate', $row->id); // example

    //             /* VIEW */
    //             if (auth()->user()->hasAnyPermission(['invoice.view']) && $row->invoice) {
    //                 $viewUrl = route('invoices.show', $row->invoice->id);

    //                 $action .= '<a href="' . $viewUrl . '" class="btn btn-primary btn-sm mr-1" title="View">
    //                     <i class="fas fa-eye"></i>
    //                 </a>';
    //             }

    //             return $action ?: '-';
    //         })


    //         ->rawColumns(['checkbox', 'tenant_name', 'action', 'project_number', 'business_type', 'status', 'installment_name'])
    //         // ->rawColumns(['action'])
    //         ->with(['columns' => $columns])
    //         ->toJson();
    // }

    public function getGeneratedInvoices(array $filters = [])
    {
        $query = $this->invoiceRepository->getQueryGenerated($filters);

        $columns = [
            ['data' => 'DT_RowIndex', 'name' => 'tenant_invoices.id'],
            ['data' => 'project_number', 'name' => 'project_number'],
            ['data' => 'company_name', 'name' => 'company_name'],
            ['data' => 'tenant_name', 'name' => 'tenant_name'],
            ['data' => 'property_name', 'name' => 'property_name'],
            ['data' => 'unit_number', 'name' => 'unit_number'],
            ['data' => 'subunit_no', 'name' => 'subunit_no'],
            ['data' => 'payment_date', 'name' => 'payment_date'],
            ['data' => 'payment_mode_name', 'name' => 'payment_mode_name'],
            ['data' => 'payment_amount', 'name' => 'payment_amount'],
            ['data' => 'installment_name', 'name' => 'installment_name'],
            ['data' => 'status', 'name' => 'status'],
        ];

        // Preload installments to avoid N+1
        $unitIds = TenantInvoice::query()
            ->whereHas('agreementPaymentDetail.agreement.contract', function ($q) {
                $q->where('contract_type_id', 1)
                    ->whereHas('contract_unit', function ($q2) {
                        $q2->where('business_type', 1);
                    });
            })
            ->join('agreement_payment_details', 'tenant_invoices.agreement_payment_detail_id', '=', 'agreement_payment_details.id')
            ->pluck('agreement_payment_details.agreement_unit_id')
            ->filter()
            ->unique();

        $installmentCache = AgreementPaymentDetail::whereIn('agreement_unit_id', $unitIds)
            ->select('id', 'agreement_unit_id', 'payment_date')
            ->orderBy('payment_date')
            ->get()
            ->groupBy('agreement_unit_id');

        return datatables()
            ->of($query)
            ->addIndexColumn()
            ->addColumn('checkbox', function ($row) {
                return '
            <div class="icheck-primary d-inline">
                <input type="checkbox"
                    class="groupCheckbox"
                    name="installment_id[]"
                    id="ichek' . $row->id . '"
                    value="' . $row->id . '">
                <label for="ichek' . $row->id . '"></label>
            </div>';
            })
            ->addColumn('project_number', function ($row) {
                $apd = $row->agreementPaymentDetail;
                $number = 'P - ' . ($apd?->agreement?->contract?->project_number ?? '-');
                $type = $apd?->agreement?->contract?->contract_type?->contract_type ?? '-';
                $b_type_id = $apd?->agreement?->contract?->contract_unit?->business_type;
                $b_type = $apd?->agreement?->contract?->contract_unit?->business_type() ?? '-';

                $badgeClass = match ($apd?->agreement?->contract?->contract_type_id) {
                    1 => 'badge badge-df text-dark',
                    2 => 'badge badge-ff text-dark',
                    default => 'badge badge-secondary',
                };

                $businessClass = ($b_type_id == 1) ? 'text-olive' : 'text-cyan';

                return "<strong>{$number}</strong>
                <p class='mb-0'>
                    <span class='{$badgeClass}'>{$type}</span>
                </p>
                <strong class='{$businessClass}'>{$b_type}</strong>";
            })
            ->addColumn('company_name', function ($row) {
                return $row->agreementPaymentDetail?->agreement?->contract?->company?->company_name ?? '-';
            })
            ->addColumn('tenant_name', function ($row) {
                $apd = $row->agreementPaymentDetail;
                $name  = $apd?->agreement?->tenant?->tenant_name ?? '-';
                $email = $apd?->agreement?->tenant?->tenant_email ?? '-';
                $phone = $apd?->agreement?->tenant?->tenant_mobile ?? '-';

                return "<strong class='text-capitalize'>{$name}</strong>
                <p class='mb-0 text-primary'>{$email}</p>
                <p class='text-muted small'>
                    <i class='fa fa-phone-alt text-danger'></i>
                    <span class='font-weight-bold'>{$phone}</span>
                </p>";
            })
            ->addColumn('property_name', function ($row) {
                return $row->agreementPaymentDetail?->agreement?->contract?->property?->property_name ?? '-';
            })
            ->addColumn('unit_number', function ($row) {
                $apd  = $row->agreementPaymentDetail;
                $unit = $apd?->agreement?->agreement_units
                    ?->firstWhere('id', $apd?->agreement_unit_id);

                return $unit?->contractUnitDetail?->unit_number ?? '-';
            })
            ->addColumn('subunit_no', function ($row) {
                $apd  = $row->agreementPaymentDetail;
                $unit = $apd?->agreement?->agreement_units
                    ?->firstWhere('id', $apd?->agreement_unit_id);

                return $unit?->contractSubunitDetail?->subunit_no ?? '-';
            })
            ->addColumn('payment_date', function ($row) {
                $date = $row->agreementPaymentDetail?->payment_date;
                return $date ? Carbon::parse($date)->format('d-m-Y') : '-';
            })
            ->addColumn('payment_mode_name', function ($row) {
                $apd  = $row->agreementPaymentDetail;
                $text = $apd?->paymentMode?->payment_mode_name ?? '';

                if (!empty($apd?->bank_id) && $apd?->bank) {
                    $text .= ' - ' . ucfirst($apd->bank->bank_name);
                }

                if (!empty($apd?->cheque_number)) {
                    $text .= ' - ' . ucfirst($apd->cheque_number);
                }

                return $text;
            })
            ->addColumn('payment_amount', function ($row) {
                return $row->agreementPaymentDetail?->payment_amount ?? '-';
            })
            ->addColumn('installment_name', function ($row) use ($installmentCache) {
                $apd = $row->agreementPaymentDetail;

                if (empty($apd?->agreement_unit_id)) {
                    return match ((int) $apd?->transaction_type) {
                        2 => '<span class="badge bg-danger">Termination Payback</span>',
                        1 => '<span class="badge bg-success">Termination Receive</span>',
                        default => '<span class="badge bg-secondary">-</span>',
                    };
                }

                $installments = $installmentCache->get($apd->agreement_unit_id, collect());
                $total        = $installments->count();
                $current      = $installments->search(fn($i) => $i->payment_date == $apd->payment_date) + 1;

                return "{$current}/{$total}";
            })
            ->addColumn('status', function ($row) {
                return match ((int) $row->status) {
                    1 => '<span class="badge bg-info">Generated</span>',
                    2 => '<span class="badge bg-success">Approved</span>',
                    3 => '<span class="badge bg-danger">On Hold</span>
                      <i class="far fa-comments loadComments"
                         data-id="' . $row->id . '"
                         style="cursor:pointer;"></i>',
                    default => '<span class="badge bg-secondary">-</span>',
                };
            })
            ->addColumn('action', function ($row) {
                $action = '';

                if (auth()->user()->hasAnyPermission(['invoice.view'], $row->agreementPaymentDetail?->agreement?->contract->company->id)) {
                    $viewUrl = route('invoices.show', $row->id);
                    $action .= '<a href="' . $viewUrl . '" class="btn btn-primary btn-sm mr-1" target="_blank" title="View">
                    <i class="fas fa-eye"></i>
                </a>';
                }


                return $action ?: '-';
            })
            ->rawColumns(['checkbox', 'tenant_name', 'action', 'project_number', 'status', 'installment_name'])
            ->with(['columns' => $columns])
            ->toJson();
    }
}
