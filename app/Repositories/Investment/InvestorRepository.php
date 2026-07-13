<?php

namespace App\Repositories\Investment;

use App\Models\Investor;
use App\Models\InvestorLedger;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class InvestorRepository
{
    public function all()
    {
        return Investor::all();
    }
    public function allActive()
    {
        return Investor::where('status', 1)->get();
    }

    public function find($id)
    {
        return Investor::findOrFail($id);
    }

    public function getByName($investorData)
    {
        return Investor::where($investorData)->first();
    }

    public function create($data)
    {
        return Investor::create($data);
    }

    public function update(int $id, array $data)
    {
        $investor = Investor::findOrFail($id);
        $investor->update($data);

        return $investor;
    }

    public function delete($id)
    {
        $investor = $this->find($id);
        $investor->deleted_by = auth()->user()->id;
        return $investor->delete();
    }

    public function uniqInvestorName($investor_name, $company_id)
    {
        return Investor::where('area_name', $investor_name)
            ->where('company_id', $company_id)
            ->first();
    }

    public function getByCompany($company_id)
    {
        return Investor::where('company_id', $company_id)->get();
    }

    public function getQuery(array $filters = []): Builder
    {
        $query = Investor::query()
            ->with([
                'nationality',
                'paymentMode',
                'countryOfResidence',
                'payoutBatch',
                'referral',
                'investorBanks'
            ]);

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $searchLike = str_replace('-', '%', $search);

            $query->where(function ($q) use ($search, $searchLike) {

                $q->where('investor_name', 'like', "%{$search}%")
                    ->orWhereRaw("CAST(investor_mobile AS CHAR) LIKE ?", ["%{$searchLike}%"])
                    ->orWhere('investor_email', 'like', "%{$search}%")
                    ->orWhereRaw("CAST(id_number AS CHAR) LIKE ?", ["%{$searchLike}%"])
                    ->orWhereRaw("CAST(passport_number AS CHAR) LIKE ?", ["%{$searchLike}%"])
                    ->orWhereRaw('DATE_FORMAT(profit_release_date, "%Y-%m-%d") LIKE ?', ["%{$searchLike}%"])

                    ->orWhereHas('nationality', function ($q) use ($search) {
                        $q->where('nationality_name', 'like', "%{$search}%");
                    })

                    ->orWhereHas('payoutBatch', function ($q) use ($search) {
                        $q->where('batch_name', 'like', "%{$search}%");
                    })

                    ->orWhereHas('paymentMode', function ($q) use ($search) {
                        $q->where('payment_mode_name', 'like', "%{$search}%");
                    })

                    ->orWhereHas('investorBanks', function ($q) use ($search) {
                        $q->where(function ($q) use ($search) {
                            $q->where('investor_beneficiary', 'like', "%{$search}%")
                                ->orWhere('investor_bank_name', 'like', "%{$search}%")
                                ->orWhere('investor_iban', 'like', "%{$search}%");
                        });
                    })

                    ->orWhereHas('referral', function ($q) use ($search) {
                        $q->where('investor_name', 'like', "%{$search}%");
                    });
            });
        }

        return $query; // ✅ ALWAYS return Builder
    }


    public function insertBulk(array $rows)
    {
        return Investor::insert($rows); // bulk insert
    }

    public function getInvestorsWithDetails()
    {
        $investors = Investor::with('payoutBatch', 'investorBanks', 'referral')
            // ->where('status', 1)
            ->get();

        return $investors;
    }
    public function updateOnInvestmentDelete($id, $investment)
    {
        DB::transaction(function () use ($id, $investment) {

            $investor = Investor::find($id);

            // Decrement total investments
            if ($investor->total_no_of_investments > 0) {
                $investor->total_no_of_investments -= 1;
            }

            // Decrement invested amount
            if ($investor->total_invested_amount > 0) {
                $investor->total_invested_amount -= $investment->investment_amount;
            }
            if ($investor->total_no_of_investments === 0) {
                $investor->status = 0;
            }

            $investor->save();

            // Handle referral
            if ($investment->investmentReferral) {
                $investor_referrer_id = $investment->investmentReferral->investor_referror_id;
                $commission_amount    = $investment->investmentReferral->referral_commission_amount;

                $referrer = Investor::find($investor_referrer_id);

                if ($referrer && $referrer->total_referal_commission > 0) {
                    $referrer->total_referal_commission -= $commission_amount;
                    $referrer->save();
                }
            }
        });
    }
    // public function getInvestedCompanies($investor_id)
    // {
    //     return Investor::with('investments.company', 'investorLedgers')
    //         ->find($investor_id)
    //         ->investments
    //         ->pluck('company')
    //         ->unique('id')
    //         ->values();
    // }
    // public function getInvestedCompanies($investor_id)
    // {
    //     $investor = Investor::with('investments.company', 'investorLedgers')
    //         ->find($investor_id);

    //     $companies = $investor->investments
    //         ->pluck('company')
    //         ->unique('id')
    //         ->values();

    //     // Group the already-loaded ledger rows by company_id (no extra query)
    //     $ledgerByCompany = $investor->investorLedgers
    //         ->where('status', 1) // adjust to your active/approved status value
    //         ->groupBy('company_id');

    //     return $companies->map(function ($company) use ($ledgerByCompany) {
    //         $entries = $ledgerByCompany->get($company->id, collect());

    //         $credit = $entries->where('is_credit', 1)->sum('transaction_amount');
    //         $debit  = $entries->where('is_credit', 0)->sum('transaction_amount');

    //         $company->total_invested  = $credit;
    //         $company->total_withdrawn = $debit;
    //         $company->balance         = $credit - $debit;

    //         return $company;
    //     });
    // }
    public function getInvestedCompanies($investor_id)
    {
        // 1. Get investor with all required relationships
        $investor = Investor::with([
            'investments.company',
            'investorLedgers.transactionType',
            'investorLedgers.investment'
        ])->find($investor_id);

        if (!$investor) {
            return collect(); // safety check
        }

        // 2. Get unique companies from investments
        $companies = $investor->investments
            ->pluck('company')     // extract company
            ->filter()             // remove nulls (important)
            ->unique('id')         // remove duplicates
            ->values();

        // 3. Filter & group ledger entries by company
        $ledgerByCompany = $investor->investorLedgers
            ->where('status', 1) // only active records
            ->sortBy('transaction_date')
            ->groupBy('company_id');

        // 4. Attach financial data to each company
        foreach ($companies as $company) {

            // Get ledger entries for this company
            $entries = $ledgerByCompany->get($company->id, collect());

            // 4.1 Calculate totals
            $totalCredit = $entries->where('is_credit', 1)->sum('transaction_amount');
            $totalDebit  = $entries->where('is_credit', 0)->sum('transaction_amount');

            $company->total_invested  = $totalCredit;
            $company->total_withdrawn = $totalDebit;
            $company->balance         = $totalCredit - $totalDebit;

            // 4.2 Build ledger with running balance
            $runningBalance = 0;

            $company->ledger = $entries->map(function ($entry) use (&$runningBalance) {

                $amount = (float) $entry->transaction_amount;

                // Determine debit/credit
                if ($entry->is_credit) {
                    $credit = $amount;
                    $debit  = 0;
                } else {
                    $credit = 0;
                    $debit  = $amount;
                }

                // Update running balance
                $runningBalance += ($credit - $debit);

                return [
                    'date'        => $entry->transaction_date,
                    'type'        => $entry->transactionType->transaction_type ?? '-',
                    // 'description' => $this->buildDescription($entry),
                    'debit'       => $debit,
                    'credit'      => $credit,
                    'balance'     => $runningBalance,
                ];
            })->values();
        }

        return $companies;
    }
    private function buildDescription($entry)
    {
        $type = $entry->transactionType->name ?? '';

        if ($entry->investment) {
            $ref = $entry->investment->reference ?? 'INV-' . $entry->investment->id;
            return $type . ' - ' . $ref;
        }

        return $type ?: '-';
    }


    public function getLedgerQuery(array $filters = []): Builder
    {
        $query = InvestorLedger::query()
            ->with(['transactionType', 'investment'])
            ->where('status', 1);

        // Filter by investor
        if (!empty($filters['investor_id'])) {
            $query->where('investor_id', $filters['investor_id']);
        }

        // Filter by company
        if (!empty($filters['company_id'])) {
            $query->where('company_id', $filters['company_id']);
        }

        // Search (optional)
        if (!empty($filters['search'])) {
            $search = trim($filters['search']);

            $query->where(function ($q) use ($search) {
                $q->where('transaction_amount', 'like', "%{$search}%")
                    ->orWhereHas('transactionType', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        return $query->orderBy('transaction_date', 'asc');
    }

    public function getPartialWithdrawalsQuery(array $filters = []): Builder
    {
        $query = InvestorLedger::query()
            ->with(['transactionType', 'investor'])
            ->where('status', 1)
            ->where('investor_transaction_type_id', 3);

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $searchLike = str_replace('-', '%', $search);

            $query->where(function ($q) use ($search, $searchLike) {

                $q->where('investor_name', 'like', "%{$search}%")
                    ->orWhereRaw("CAST(investor_mobile AS CHAR) LIKE ?", ["%{$searchLike}%"])
                    ->orWhere('investor_email', 'like', "%{$search}%")
                    ->orWhereRaw("CAST(id_number AS CHAR) LIKE ?", ["%{$searchLike}%"])
                    ->orWhereRaw("CAST(passport_number AS CHAR) LIKE ?", ["%{$searchLike}%"])
                    ->orWhereRaw('DATE_FORMAT(profit_release_date, "%Y-%m-%d") LIKE ?', ["%{$searchLike}%"])

                    ->orWhereHas('nationality', function ($q) use ($search) {
                        $q->where('nationality_name', 'like', "%{$search}%");
                    })

                    ->orWhereHas('payoutBatch', function ($q) use ($search) {
                        $q->where('batch_name', 'like', "%{$search}%");
                    })

                    ->orWhereHas('paymentMode', function ($q) use ($search) {
                        $q->where('payment_mode_name', 'like', "%{$search}%");
                    })

                    ->orWhereHas('investorBanks', function ($q) use ($search) {
                        $q->where(function ($q) use ($search) {
                            $q->where('investor_beneficiary', 'like', "%{$search}%")
                                ->orWhere('investor_bank_name', 'like', "%{$search}%")
                                ->orWhere('investor_iban', 'like', "%{$search}%");
                        });
                    })

                    ->orWhereHas('referral', function ($q) use ($search) {
                        $q->where('investor_name', 'like', "%{$search}%");
                    });
            });
        }

        return $query; // ✅ ALWAYS return Builder
    }
}
