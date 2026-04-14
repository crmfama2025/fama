<?php

namespace App\Services\Agreement;

use App\Models\Agreement;
use App\Repositories\Agreement\AgreementDocRepository;

use App\Repositories\Agreement\AgreementRepository;
use App\Repositories\Agreement\AgreementUnitRepository;
use App\Repositories\Agreement\InvoiceRepository;
use App\Services\PdfCompressionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class InvoiceService
{
    public function __construct(
        protected AgreementRepository $agreementRepository,
        protected AgreementDocRepository $agreementDocRepository,
        protected AgreementUnitRepository $agreementUnitRepository,
        protected InvoiceRepository $invoiceRepository

    ) {}

    private function validate(array $data, $id = null)
    {
        $validator = Validator::make($data, [
            'invoice_path' => 'required|file|mimes:pdf,jpg,jpeg,png',
            'agreement_id' => 'required|integer',
            'detail_id' => 'required|integer',
            'invoice_id' => 'nullable|integer',
        ], [
            'invoice_path.mimes' => 'The invoice must be a PDF or image (jpg, jpeg, png).',
            'invoice_path.required' => 'The invoice file is required.',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }


    public function upload_invoice($data)
    {
        $this->validate($data);

        $agreement = Agreement::with('contract')->findOrFail($data['agreement_id']);
        $code = $agreement->agreement_code;
        $project_code = $agreement->contract->project_code;

        $filename = uniqid() . '_' . $data['invoice_path']->getClientOriginalName();
        // $path = $data['invoice_path']->storeAs(
        //     "projects/{$project_code}/agreements/{$code}/tenant-invoices",
        //     $filename,
        //     'public'
        // );


        // Upload with file size compression
        $pdfservice = new PdfCompressionService();
        if ($data['invoice_path']->getClientOriginalExtension() == 'pdf') {
            // dd("test");

            $path = $pdfservice->compress(
                $data['invoice_path'],
                'projects/' . $project_code . '/agreements/' . $code . '/tenant-invoices',
                $filename
            );
        } else {
            $path = $data['invoice_path']->storeAs("projects/{$project_code}/agreements/{$code}/tenant-invoices", $filename, 'public');
        }

        if (!empty($data['invoice_id'])) {
            $invoice = $this->invoiceRepository->find($data['invoice_id']);

            if ($invoice->invoice_path && Storage::disk('public')->exists($invoice->invoice_path)) {
                Storage::disk('public')->delete($invoice->invoice_path);
            }

            $data['invoice_path'] = $path;
            $data['invoice_file_name'] = $filename;
            $data['agreement_payment_detail_id'] = $data['detail_id'];
            $data['updated_by'] = auth()->user()->id;
            return $this->invoiceRepository->update_invoice($data);
        } else {
            $data['invoice_path'] = $path;
            $data['invoice_file_name'] = $filename;
            $data['agreement_payment_detail_id'] = $data['detail_id'];
            $data['added_by'] = auth()->user()->id;
            return $this->invoiceRepository->create_invoice($data);
        }
    }
}
