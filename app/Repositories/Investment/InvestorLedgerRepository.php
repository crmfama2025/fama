<?php

namespace App\Repositories\Investment;

use App\Models\InvestmentDocument;
use App\Models\InvestorLedger;
use App\Models\PartialWithdrawalBifurcation;
use Illuminate\Contracts\Database\Eloquent\Builder;

class InvestorLedgerRepository
{
    public function all()
    {
        return InvestorLedger::all();
    }

    public function find($id)
    {
        return InvestorLedger::findOrFail($id);
    }
    public function findByDocId($id)
    {
        return InvestorLedger::where('investment_contract_document_id', $id)->first();
    }

    public function getfirstbyCond($condition)
    {
        return InvestorLedger::where($condition)->first();
    }

    public function create($data)
    {
        return InvestorLedger::create($data);
    }
    public function update(int $id, array $data)
    {
        $investmentDocument = InvestorLedger::findOrFail($id);
        return $investmentDocument->update($data);
    }
    public function getQuery(array $filters = []): Builder
    {
        $permittedCompanyIds = getUserPermittedCompanyIds(auth()->user()->id, 'investment');

        $query = InvestorLedger::with('investor', 'investment', 'investment.company', 'transactionType');

        $query->whereHas('investment.company', function ($q) use ($permittedCompanyIds) {
            $q->whereIn('company_id', $permittedCompanyIds);
        });
        if (!empty($filters['investment_id'])) {
            $query->where('investment_id', $filters['investment_id']);
        }
        // dd($filters);

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
                ->orWhereHas('investment', function ($q) use ($filters) {
                    $q->where('investment_code', 'like', '%' . $filters['search'] . '%');
                })

                ->orWhereHas('transactionType', function ($q) use ($filters) {
                    $q->where('transaction_type', 'like', '%' . $filters['search'] . '%');
                })

                ->orWhereHas('investment.company', function ($q) use ($filters) {
                    $q->where('company_name', 'like', '%' . $filters['search'] . '%');
                })
                ->orWhereRaw("CAST(investment_contract_documents.id AS CHAR) LIKE ?", ['%' . $filters['search'] . '%']);
        }

        // if (!empty($filters['company_id'])) {
        //     $query->Where('company_id', $filters['company_id']);
        // }

        return $query;
    }
    public function getDetails($id)
    {
        return InvestorLedger::with([
            'investor',
            'investment',
            'transactionType'
        ])->findOrFail($id);
    }
    public function createPartialWithdrawal($data)
    {
        return PartialWithdrawalBifurcation::create($data);
    }
    // InvestorLedgerRepository
    public function getBifurcationsByLedgerId($ledgerId)
    {
        return PartialWithdrawalBifurcation::where('ledger_id', $ledgerId)->get();
    }

    public function deleteBifurcationsByLedgerId($ledgerId)
    {
        return PartialWithdrawalBifurcation::where('ledger_id', $ledgerId)->delete();
    }

    public function investmentHasOtherActiveBifurcation($investmentId, $excludeLedgerId)
    {
        return PartialWithdrawalBifurcation::where('investment_id', $investmentId)
            ->where('ledger_id', '!=', $excludeLedgerId)
            ->exists();
    }
}
