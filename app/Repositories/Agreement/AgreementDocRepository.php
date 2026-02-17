<?php

namespace App\Repositories\Agreement;

use App\Models\Agreement;
use App\Models\AgreementDocument;
use App\Models\Contract;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class AgreementDocRepository
{



    public function create(array $data)
    {
        return AgreementDocument::create($data);
    }
    public function update(array $data)
    {
        return AgreementDocument::update($data);
    }
    public function getDocuments($id)
    {
        return AgreementDocument::with('TenantIdentity')->where("agreement_id", $id)->get();
    }
    // public function getDocumentsQuery(array $filters = []) {}
    public function getDocumentsQuery(array $filters = [])
    {
        $today = Carbon::today();
        $oneMonthLater = Carbon::today()->addMonth();

        $query = AgreementDocument::with(['agreement', 'agreement.tenant', 'TenantIdentity',])
            // ->whereBetween('expiry_date', [$today, $oneMonthLater]);
            ->where('expiry_date', '<=', $oneMonthLater);
        // ->get();

        // $get = $query->get();
        // dd($get);
        // Parse date once outside — reuse inside
        $search = $filters['search'];
        $parsedDate = null;
        try {
            $parsedDate = \Carbon\Carbon::createFromFormat('d-m-Y', $search)->format('Y-m-d');
        } catch (\Exception $e) {
            // not a valid d-m-Y date
        }

        $query->where(function ($q) use ($search, $parsedDate) {

            $q->where('document_number', 'like', '%' . $search . '%')

                ->orWhereHas('TenantIdentity', function ($q) use ($search) {
                    $q->where('identity_type', 'like', '%' . $search . '%');
                })

                ->orWhereHas('agreement.tenant', function ($q) use ($search) {
                    $q->where('tenant_name', 'like', '%' . $search . '%')
                        ->orWhere('tenant_email', 'like', '%' . $search . '%')
                        ->orWhere('tenant_mobile', 'like', '%' . $search . '%');
                })

                ->orWhereHas('agreement', function ($q) use ($search) {
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

        $query->orderBy('agreement_documents.id', 'desc');
        // $result = $query->get();
        // dd($result);

        return $query;
    }
}
