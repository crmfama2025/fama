<?php

namespace App\Repositories\Investment;

use App\Models\InvestmentContractDocuments;
use App\Models\InvestmentDocument;
use Illuminate\Contracts\Database\Eloquent\Builder;

class InvestmentContractDocumentRepository
{
    public function all()
    {
        return InvestmentContractDocuments::all();
    }

    public function find($id)
    {
        return InvestmentContractDocuments::findOrFail($id);
    }


    public function create($data)
    {
        return InvestmentContractDocuments::create($data);
    }
    public function update(int $id, array $data)
    {
        $investmentDocument = InvestmentContractDocuments::findOrFail($id);
        return $investmentDocument->update($data);
    }
    public function getQuery(array $filters = []): Builder
    {
        $permittedCompanyIds = getUserPermittedCompanyIds(auth()->user()->id, 'investment');

        $query = InvestmentContractDocuments::with('investor', 'investment', 'investment.company', 'agreementType', 'agreementTemplate');

        $query->whereHas('investment.company', function ($q) use ($permittedCompanyIds) {
            $q->whereIn('company_id', $permittedCompanyIds);
        });
        if (!empty($filters['investment_id'])) {
            $query->where('investment_id', $filters['investment_id']);
        }


        $result = $query->get();
        // dd($result);
        if (!empty($filters['search'])) {
            $query->orWhere('investment_amount', 'like', '%' . $filters['search'] . '%')
                ->orWhere('investment_date', 'like', '%' . $filters['search'] . '%')
                ->orWhere('investment_code', 'like', '%' . $filters['search'] . '%')
                ->orWhere('maturity_date', 'like', '%' . $filters['search'] . '%')
                ->orWhere('profit_perc', 'like', '%' . $filters['search'] . '%')
                ->orWhere('received_amount', 'like', '%' . $filters['search'] . '%')
                ->orWhere('profit_release_date', 'like', '%' . $filters['search'] . '%')
                ->orWhere('nominee_name', 'like', '%' . $filters['search'] . '%')
                ->orWhere('nominee_email', 'like', '%' . $filters['search'] . '%')
                ->orWhere('nominee_phone', 'like', '%' . $filters['search'] . '%')
                ->orWhereHas('investor', function ($q) use ($filters) {
                    $q->where('investor_name', 'like', '%' . $filters['search'] . '%');
                })
                ->orWhereHas('profitInterval', function ($q) use ($filters) {
                    $q->where('profit_interval_name', 'like', '%' . $filters['search'] . '%');
                })
                ->orWhereHas('payoutBatch', function ($q) use ($filters) {
                    $q->where('batch_name', 'like', '%' . $filters['search'] . '%');
                })
                ->orWhereHas('company', function ($q) use ($filters) {
                    $q->where('company_name', 'like', '%' . $filters['search'] . '%');
                })->orWhereHas('investmentReferral', function ($q) use ($filters) {
                    $q->where('referral_commission_amount', 'like', '%' . $filters['search'] . '%');
                    $q->whereHas('referrer', function ($qr) use ($filters) {
                        $qr->where('investor_name', 'like', '%' . $filters['search'] . '%');
                    });
                })
                ->orWhereRaw("CAST(investments.id AS CHAR) LIKE ?", ['%' . $filters['search'] . '%']);
        }

        // if (!empty($filters['company_id'])) {
        //     $query->Where('company_id', $filters['company_id']);
        // }

        return $query;
    }
    public function getDetails($id)
    {
        return InvestmentContractDocuments::with([
            'investor',
            'investment',
            'agreementType',
            'agreementTemplate'
        ])->findOrFail($id);
    }
}
