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
        $investmentDocument->update($data);

        return $investmentDocument->fresh();
    }
    public function getQuery(array $filters = []): Builder
    {
        $permittedCompanyIds = getUserPermittedCompanyIds(auth()->user()->id, 'investment');

        // $query = InvestmentContractDocuments::with('investor', 'investment', 'investment.company', 'agreementType', 'agreementTemplate');
        $query = InvestmentContractDocuments::query();

        // Always needed relations
        $with = ['investor', 'agreementType', 'agreementTemplate', 'company'];

        // Only add investment if filter exists
        if (!empty($filters['investment_id'])) {
            $with[] = 'investment';
            $with[] = 'investment.company';
        }

        // Apply with
        $query->with($with);

        // $query->whereHas('investment.company', function ($q) use ($permittedCompanyIds) {
        //     $q->whereIn('company_id', $permittedCompanyIds);
        // });
        if (!empty($filters['investment_id'])) {
            $query->where('investment_id', $filters['investment_id']);
        }
        if (!empty($filters['company_id'])) {
            // dump($filters['company_id']);
            $query->whereHas('company', function ($q) use ($filters) {
                $q->where('company_id', $filters['company_id']);
            });
        }



        $result = $query->get();
        // dd($result);
        if (!empty($filters['search'])) {
            $query
                // ->orWhere('investment_amount', 'like', '%' . $filters['search'] . '%')
                // ->orWhere('investment_date', 'like', '%' . $filters['search'] . '%')
                // ->orWhere('investment_code', 'like', '%' . $filters['search'] . '%')
                // ->orWhere('maturity_date', 'like', '%' . $filters['search'] . '%')
                // ->orWhere('profit_perc', 'like', '%' . $filters['search'] . '%')
                // ->orWhere('received_amount', 'like', '%' . $filters['search'] . '%')
                // ->orWhere('profit_release_date', 'like', '%' . $filters['search'] . '%')
                // ->orWhere('nominee_name', 'like', '%' . $filters['search'] . '%')
                // ->orWhere('nominee_email', 'like', '%' . $filters['search'] . '%')
                // ->orWhere('nominee_phone', 'like', '%' . $filters['search'] . '%')
                ->WhereHas('investor', function ($q) use ($filters) {
                    $q->where('investor_name', 'like', '%' . $filters['search'] . '%');
                })
                // ->orWhereHas('investment', function ($q) use ($filters) {
                //     $q->where('investment_code', 'like', '%' . $filters['search'] . '%');
                // })

                ->orWhereHas('agreementType', function ($q) use ($filters) {
                    $q->where('investor_agreement_type', 'like', '%' . $filters['search'] . '%');
                })
                ->orWhereHas('agreementTemplate', function ($q) use ($filters) {
                    $q->whereRaw("CONCAT('V', version_no) LIKE ?", ['%' . $filters['search'] . '%']);
                })
                ->orWhereHas('company', function ($q) use ($filters) {
                    $q->where('company_name', 'like', '%' . $filters['search'] . '%');
                })
                ->orWhereRaw("CAST(investment_contract_documents.id AS CHAR) LIKE ?", ['%' . $filters['search'] . '%']);
        }

        if (isset($filters['status']) && $filters['status'] !== 'all') {
            switch ($filters['status']) {
                case 1:
                    $query->where('investment_contract_documents.is_investor_signed', 0)
                        ->where('investment_contract_documents.is_company_signed', 0);
                    break;
                case 2:
                    $query->where('investment_contract_documents.is_investor_signed', 1)
                        ->where('investment_contract_documents.is_company_signed', 0);
                    break;
                case 3:
                    $query->where('investment_contract_documents.is_investor_signed', 1)
                        ->where('investment_contract_documents.is_company_signed', 1);
                    break;
            }
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

    public function isInvestorSigned($contractId)
    {
        return InvestmentContractDocuments::where('id', $contractId)
            ->value('is_investor_signed') ?? false;
    }
}
