<?php

namespace App\Repositories\Investment;

use App\Models\Investment;
use App\Models\InvestmentReceivedPayment;
use App\Models\InvestmentReferral;
use Illuminate\Contracts\Database\Eloquent\Builder;

class InvestmentRepository
{
    public function all()
    {
        return Investment::all();
    }

    public function find($id)
    {
        return Investment::findOrFail($id);
    }
    public function getWithDetails($id)
    {
        return Investment::with([
            'investor.investorBanks',
            'company.banks',
            'profitInterval',
            'payoutBatch',
            'investmentReferral',
            'investmentDocument',
            'investmentReceivedPayments'
        ])->findOrFail($id);
    }


    public function getByName($areaData)
    {
        return Investment::where($areaData)->first();
    }

    public function create($data)
    {
        return Investment::create($data);
    }
    public function updateById(int $id, array $data)
    {
        $investment = Investment::findOrFail($id);
        return $investment->update($data);
    }

    public function update(int $id, array $data)
    {
        $investment = Investment::findOrFail($id);
        $investment->update($data);

        return $investment;
    }

    public function delete($id)
    {
        $investment = $this->find($id);
        return $investment->delete();
    }

    public function uniqInvestmentName($area_name, $company_id)
    {
        return Investment::where('area_name', $area_name)
            ->where('company_id', $company_id)
            ->first();
    }

    public function getByCompany($company_id)
    {
        return Investment::where('company_id', $company_id)->get();
    }

    public function getQuery(array $filters = []): Builder
    {
        $query = Investment::with('investor', 'payoutBatch', 'profitInterval', 'company', 'investmentReferral', 'investedCompany');
        if (!empty($filters['investor_id'])) {
            $query->where('investor_id', $filters['investor_id']);
        }
        $result = $query->get();
        // dd($result);
        if (!empty($filters['search'])) {
            $query->orWhere('investment_amount', 'like', '%' . $filters['search'] . '%')
                ->orWhere('investment_date', 'like', '%' . $filters['search'] . '%')
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

    public function getTotalReceivedAmount($investment)
    {
        return InvestmentReceivedPayment::where('investment_id', $investment->id)
            ->sum('received_amount');
    }

    public function insertBulk(array $rows)
    {
        return Investment::insert($rows);
    }
    public function getDetails($id)
    {
        return Investment::with([
            'investor',
            'company',
            'profitInterval',
            'payoutBatch',
            'investmentReferral.referrer',
            'investmentDocument',
            'investmentReceivedPayments',
            'investedCompany'
        ])->findOrFail($id);
    }

    public function getActiveInvestmentByInvestment($investorid)
    {
        return Investment::where(array('investor_id' => $investorid, 'investment_status' => 1))->get();
    }
    public function getReferralQuery(array $filters = []): Builder
    {
        $query = InvestmentReferral::with('referrer', 'investment', 'investor', 'commissionFrequency', 'investment');

        $result = $query->get();
        // dd($result);
        if (!empty($filters['search'])) {
            $query->orWhere('referral_commission_perc', 'like', '%' . $filters['search'] . '%')
                ->orWhere('referral_commission_amount', 'like', '%' . $filters['search'] . '%')

                ->orWhereHas('investor', function ($q) use ($filters) {
                    $q->where('investor_name', 'like', '%' . $filters['search'] . '%');
                })
                ->orWhereHas('referrer', function ($q) use ($filters) {
                    $q->where('investor_name', 'like', '%' . $filters['search'] . '%');
                })
                ->orWhereHas('commissionFrequency', function ($q) use ($filters) {
                    $q->where('commission_frequency_name', 'like', '%' . $filters['search'] . '%');
                })
                ->orWhereHas('investment', function ($q) use ($filters) {

                    $search = $filters['search'];

                    try {
                        $date = \Carbon\Carbon::createFromFormat('d-m-Y', $search)->format('Y-m-d');

                        $q->whereDate('investment_date', $date);
                    } catch (\Exception $e) {
                        $q->where('investment_date', 'like', '%' . $search . '%');
                    }
                })


                ->orWhereRaw("CAST(investment_referrals.id AS CHAR) LIKE ?", ['%' . $filters['search'] . '%']);
        }

        // if (!empty($filters['company_id'])) {
        //     $query->Where('company_id', $filters['company_id']);
        // }

        return $query;
    }
}
