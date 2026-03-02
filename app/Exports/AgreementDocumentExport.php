<?php

namespace App\Exports;

use App\Models\AgreementDocument;
use App\Models\TenantDocument;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AgreementDocumentExport implements FromCollection, WithHeadings, WithMapping
{
    protected $search;
    protected $filters;

    public function __construct($search = null, array $filters = [])
    {
        $this->search = $search;
        $this->filters = $filters;
    }

    public function collection()
    {
        $oneMonthLater = Carbon::today()->addMonth();

        // $query = AgreementDocument::with([
        //     'agreement.tenant',
        //     'TenantIdentity',
        // ])
        // ->where('expiry_date', '<=', $oneMonthLater);
        $query = TenantDocument::with(['tenant', 'tenant.agreement', 'TenantIdentity',])
            // ->whereBetween('expiry_date', [$today, $oneMonthLater]);
            ->where('expiry_date', '<=', $oneMonthLater);

        // ✅ Use $this->search, not $filters['search']
        $search = $this->search;

        if (!empty($search)) {
            $parsedDate = null;
            try {
                $parsedDate = Carbon::createFromFormat('d-m-Y', $search)->format('Y-m-d');
            } catch (\Exception $e) {
                // not a valid d-m-Y date
            }

            $query->where(function ($q) use ($search, $parsedDate) {
                $q->where('document_number', 'like', '%' . $search . '%')

                    ->orWhereHas('TenantIdentity', function ($q) use ($search) {
                        $q->where('identity_type', 'like', '%' . $search . '%');
                    })

                    ->orWhereHas('tenant', function ($q) use ($search) {
                        $q->where('tenant_name', 'like', '%' . $search . '%')
                            ->orWhere('tenant_email', 'like', '%' . $search . '%')
                            ->orWhere('tenant_mobile', 'like', '%' . $search . '%')
                            ->orWhereRaw("
                            CASE
                                WHEN tenant_type = 1 THEN 'B2B'
                                WHEN tenant_type = 2 THEN 'B2C'
                            END LIKE ?
                        ", ["%{$search}%"]);
                    })

                    ->orWhereHas('tenant.agreement', function ($q) use ($search) {
                        $q->where('agreement_code', 'like', '%' . $search . '%');
                    })

                    ->orWhere(function ($q) use ($search, $parsedDate) {
                        if ($parsedDate) {
                            $q->whereDate('issued_date', $parsedDate);
                        } else {
                            $q->whereRaw("DATE_FORMAT(issued_date, '%d-%m-%Y') LIKE ?", ['%' . $search . '%'])
                                ->orWhereRaw("DATE_FORMAT(issued_date, '%Y-%m-%d') LIKE ?", ['%' . $search . '%']);
                        }
                    })

                    ->orWhere(function ($q) use ($search, $parsedDate) {
                        if ($parsedDate) {
                            $q->whereDate('expiry_date', $parsedDate);
                        } else {
                            $q->whereRaw("DATE_FORMAT(expiry_date, '%d-%m-%Y') LIKE ?", ['%' . $search . '%'])
                                ->orWhereRaw("DATE_FORMAT(expiry_date, '%Y-%m-%d') LIKE ?", ['%' . $search . '%']);
                        }
                    });
            });
        }

        $query->orderBy('tenant_documents.id', 'desc');

        return $query->get();
    }

    public function headings(): array
    {
        return [
            // '#',
            'Document Type',
            'Document Number',
            'Issued Date',
            'Expiry Date',
            'Tenant Name',
            'Tenant Email',
            'Tenant Mobile',
            'Tenant Code',
            'Tenant Type',
        ];
    }

    public function map($row): array
    {
        return [
            // $row->id,
            $row->TenantIdentity->identity_type ?? '-',
            $row->document_number ?? '-',
            $row->issued_date ? Carbon::parse($row->issued_date)->format('d-m-Y') : '-',
            $row->expiry_date ? Carbon::parse($row->expiry_date)->format('d-m-Y') : '-',
            $row->tenant->tenant_name ?? '-',
            $row->tenant->tenant_email ?? '-',
            $row->tenant->tenant_mobile ?? '-',
            $row->tenant->tenant_code ?? '-',
            $row->tenant->tenant_type == 1 ? 'B2B' : ($row->tenant->tenant_type == 2 ? 'B2C' : '-'),
        ];
    }
}
