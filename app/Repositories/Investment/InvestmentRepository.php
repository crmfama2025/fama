<?php

namespace App\Repositories\Investment;

use App\Models\Investment;
use App\Models\InvestmentReceivedPayment;
use App\Models\InvestmentReferral;
use App\Models\InvestmentProfitRecord;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

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
            'investmentReceivedPayments',
            'companyAllocations.company',
        ])->findOrFail($id);
    }


    public function getByName($areaData)
    {
        return Investment::where($areaData)->first();
    }

    public function getAllByCondition($where, array $whereIn = [])
    {
        $query = Investment::where($where);

        foreach ($whereIn as $column => $values) {
            $query->whereIn($column, (array) $values);
        }

        return $query->get();
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

    // public function getQuery(array $filters = []): Builder
    // {
    //     $permittedCompanyIds = getUserPermittedCompanyIds(auth()->user()->id, 'investment');

    //     $query = Investment::with('investor', 'payoutBatch', 'profitInterval', 'company', 'investmentReferral', 'companyAllocations.company', 'investedCompany');

    //     $query->whereHas('company', function ($q) use ($permittedCompanyIds) {
    //         $q->whereIn('company_id', $permittedCompanyIds);
    //     });

    //     if (!empty($filters['investor_id'])) {
    //         $query->where('investor_id', $filters['investor_id']);
    //     }
    //     $result = $query->get();

    //     if (!empty($filters['search'])) {
    //         $query->orWhere('investment_amount', 'like', '%' . $filters['search'] . '%')
    //             ->orWhere('investment_date', 'like', '%' . $filters['search'] . '%')
    //             ->orWhere('investment_code', 'like', '%' . $filters['search'] . '%')
    //             ->orWhere('maturity_date', 'like', '%' . $filters['search'] . '%')
    //             ->orWhere('profit_perc', 'like', '%' . $filters['search'] . '%')
    //             ->orWhere('received_amount', 'like', '%' . $filters['search'] . '%')
    //             ->orWhere('profit_release_date', 'like', '%' . $filters['search'] . '%')
    //             ->orWhere('nominee_name', 'like', '%' . $filters['search'] . '%')
    //             ->orWhere('nominee_email', 'like', '%' . $filters['search'] . '%')
    //             ->orWhere('nominee_phone', 'like', '%' . $filters['search'] . '%')
    //             ->orWhereHas('investor', function ($q) use ($filters) {
    //                 $q->where('investor_name', 'like', '%' . $filters['search'] . '%');
    //             })
    //             ->orWhereHas('profitInterval', function ($q) use ($filters) {
    //                 $q->where('profit_interval_name', 'like', '%' . $filters['search'] . '%');
    //             })
    //             ->orWhereHas('payoutBatch', function ($q) use ($filters) {
    //                 $q->where('batch_name', 'like', '%' . $filters['search'] . '%');
    //             })
    //             ->orWhereHas('company', function ($q) use ($filters) {
    //                 $q->where('company_name', 'like', '%' . $filters['search'] . '%');
    //             })->orWhereHas('investmentReferral', function ($q) use ($filters) {
    //                 $q->where('referral_commission_amount', 'like', '%' . $filters['search'] . '%');
    //                 $q->whereHas('referrer', function ($qr) use ($filters) {
    //                     $qr->where('investor_name', 'like', '%' . $filters['search'] . '%');
    //                 });
    //             })
    //             ->orWhereRaw("CAST(investments.id AS CHAR) LIKE ?", ['%' . $filters['search'] . '%']);
    //     }

    //     // if (!empty($filters['company_id'])) {
    //     //     $query->Where('company_id', $filters['company_id']);
    //     // }

    //     return $query;
    // }
    public function getQuery(array $filters = []): Builder
    {

        $permittedCompanyIds = getUserPermittedCompanyIds(
            auth()->user()->id,
            'investment'
        );

        $query = Investment::with([
            'investor',
            'payoutBatch',
            'profitInterval',
            'company',
            'investmentReferral',
            'companyAllocations.company',
            'investedCompany',
        ]);

        $query->whereHas('company', function ($q) use ($permittedCompanyIds) {
            $q->whereIn('company_id', $permittedCompanyIds);
        });

        if (!empty($filters['investor_id'])) {
            $query->where('investor_id', $filters['investor_id']);
        }

        if (!empty($filters['company_id'])) {
            $query->where('company_id', $filters['company_id']);
        }

        if (
            isset($filters['investment_term_type']) &&
            $filters['investment_term_type'] !== ''
        ) {
            $query->where(
                'investment_term_type',
                (int) $filters['investment_term_type']
            );
        }

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $like = '%' . $search . '%';

            $query->where(function ($q) use ($like) {
                $q->where('investment_amount', 'like', $like)
                    ->orWhere('investment_date', 'like', $like)
                    ->orWhere('investment_code', 'like', $like)
                    ->orWhere('maturity_date', 'like', $like)
                    ->orWhere('profit_perc', 'like', $like)
                    ->orWhere('received_amount', 'like', $like)
                    ->orWhere('total_received_amount', 'like', $like)
                    ->orWhere('profit_release_date', 'like', $like)
                    ->orWhere('nominee_name', 'like', $like)
                    ->orWhere('nominee_email', 'like', $like)
                    ->orWhere('nominee_phone', 'like', $like)

                    ->orWhereHas('investor', function ($investorQuery) use ($like) {
                        $investorQuery->where('investor_name', 'like', $like)
                            ->orWhere('investor_code', 'like', $like);
                    })

                    ->orWhereHas('profitInterval', function ($intervalQuery) use ($like) {
                        $intervalQuery->where('profit_interval_name', 'like', $like);
                    })

                    ->orWhereHas('payoutBatch', function ($batchQuery) use ($like) {
                        $batchQuery->where('batch_name', 'like', $like);
                    })

                    ->orWhereHas('company', function ($companyQuery) use ($like) {
                        $companyQuery->where('company_name', 'like', $like);
                    })

                    ->orWhereHas('companyAllocations.company', function ($companyQuery) use ($like) {
                        $companyQuery->where('company_name', 'like', $like);
                    })

                    ->orWhereHas('investmentReferral', function ($referralQuery) use ($like) {
                        $referralQuery
                            ->where('referral_commission_amount', 'like', $like)
                            ->orWhereHas('referrer', function ($referrerQuery) use ($like) {
                                $referrerQuery->where('investor_name', 'like', $like);
                            });
                    })
                    ->orWhereHas('addedBy', function ($userQuery) use ($like) {
                        $userQuery->where(function ($user) use ($like) {
                            $user->where('first_name', 'like', $like)
                                ->orWhere('last_name', 'like', $like)
                                ->orWhereRaw(
                                    "CONCAT_WS(' ', first_name, last_name) LIKE ?",
                                    [$like]
                                );
                        });
                    })

                    ->orWhereRaw(
                        'CAST(investments.id AS CHAR) LIKE ?',
                        [$like]
                    );
            });
        }

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
        $permittedCompanyIds = getUserPermittedCompanyIds(auth()->user()->id, 'investment');

        $query = InvestmentReferral::with('referrer', 'investment', 'investor', 'commissionFrequency', 'paymentTerm');


        $query->whereHas('investment.company', function ($q) use ($permittedCompanyIds) {
            $q->whereIn('company_id', $permittedCompanyIds);
        });

        // $result = $query->get();
        // dd($result);
        if (!empty($filters['search'])) {
            // dd($filters['search']);
            $query->where('referral_commission_perc', 'like', '%' . $filters['search'] . '%')
                ->orWhere('referral_commission_amount', 'like', '%' . $filters['search'] . '%')

                ->orWhereHas('investor', function ($q) use ($filters) {
                    $q->where('investor_name', 'like', '%' . $filters['search'] . '%');
                })
                ->orWhereHas('referrer', function ($q) use ($filters) {
                    $q->where('investor_name', 'like', '%' . $filters['search'] . '%');
                })
                ->orWhereHas('commissionFrequency', function ($q) use ($filters) {
                    // dd($filters['search']);
                    $q->where('commission_frequency_name', 'like', '%' . $filters['search'] . '%');
                })
                ->orWhereHas('paymentTerm', function ($q) use ($filters) {
                    $q->where('term_name', 'like', '%' . $filters['search'] . '%');
                })
                ->orWhereHas('investment', function ($q) use ($filters) {

                    $search = $filters['search'];

                    try {
                        $date = \Carbon\Carbon::createFromFormat('d-m-Y', $search)->format('Y-m-d');
                        // dd($date);

                        $q->whereDate('investment_date', $date);
                    } catch (\Exception $e) {
                        $q->orWhere('investment_date', 'like', '%' . $search . '%');
                    }
                })


                ->orWhereRaw("CAST(investment_referrals.id AS CHAR) LIKE ?", ['%' . $filters['search'] . '%']);
        }

        // if (!empty($filters['company_id'])) {
        //     $query->Where('company_id', $filters['company_id']);
        // }

        return $query;
    }

    public function generateInvestorProfitRecords($profits, $investment)
    {
        if (InvestmentProfitRecord::where('investment_id', $investment->id)->exists()) {
            throw new \RuntimeException("Profit records already exist for investment #{$investment->id}");
        }

        $rows = $this->buildProfitScheduleFromInput($profits, $investment);

        return DB::transaction(fn() => InvestmentProfitRecord::insert($rows));
    }

    /**
     * Build insertable rows from frontend-submitted profit_records[] (date: DD-MM-YYYY, amount: numeric).
     * Replaces buildProfitSchedule() as the source of truth when the schedule was already
     * edited/confirmed in the UI — no server-side regeneration here, just validation + shaping.
     */
    protected function buildProfitScheduleFromInput(array $profits, $investment): array
    {
        $now = now();
        $rows = [];

        foreach ($profits as $record) {
            if (empty($record['date'])) {
                continue;
            }

            $amount = round((float) ($record['amount'] ?? 0), 2);

            $rows[] = [
                'investor_id'           => $investment->investor_id,
                'investment_id'         => $investment->id,
                'profit_release_month'  => Carbon::createFromFormat('d-m-Y', $record['date'])->startOfDay(),
                'profit_amount'         => $amount,
                'has_profit_amount'     => $amount > 0 ? 1 : 0,
                'release_status'        => 'pending',
                'renewal_count' => 0,
                'released_total_amount' => 0,
                'last_released_at'      => null,
                'last_released_by'      => null,
                'created_at'            => $now,
                'updated_at'            => $now,
            ];
        }

        return $rows;
    }

    /**
     * Reconcile existing PENDING profit records with a recalculated schedule.
     * Safe only when is_profit_processed = 0 (guaranteed by caller) — every row is still pending.
     */
    // public function syncInvestorProfitRecords($investment)
    // {
    //     return DB::transaction(function () use ($investment) {
    //         $newSchedule = $this->buildProfitSchedule($investment, forInsert: false);

    //         $existing = InvestmentProfitRecord::where('investment_id', $investment->id)
    //             ->where('release_status', 'pending')
    //             ->orderBy('profit_release_month')
    //             ->get();

    //         $existingCount = $existing->count();
    //         $newCount = count($newSchedule);

    //         foreach ($newSchedule as $index => $row) {
    //             if ($index < $existingCount) {
    //                 $existing[$index]->update([
    //                     'profit_release_month'   => $row['profit_release_month'],
    //                     'profit_amount' => $row['profit_amount'],
    //                     'has_profit_amount' => $row['profit_amount'] > 0 ? 1 : 0,
    //                 ]);
    //             } else {
    //                 InvestmentProfitRecord::create([
    //                     'investor_id'            => $investment->investor_id,
    //                     'investment_id'          => $investment->id,
    //                     'profit_release_month'   => $row['profit_release_month'],
    //                     'profit_amount'          => $row['profit_amount'],
    //                     'release_status'         => 'pending',
    //                     'released_total_amount'  => 0,
    //                     'last_released_at'       => null,
    //                     'last_released_by'       => null,
    //                 ]);
    //             }
    //         }

    //         if ($newCount < $existingCount) {
    //             $idsToDelete = $existing->slice($newCount)->pluck('id');
    //             InvestmentProfitRecord::whereIn('id', $idsToDelete)->delete();
    //         }
    //     });
    // }

    /**
     * Shared schedule builder used by both generate() and sync().
     */
    // protected function buildProfitSchedule($investment, bool $forInsert = true): array
    // {
    //     $schedule = [];
    //     $now = now();

    //     // Schedule starts at investment date + grace period (in days)
    //     $scheduleStart = $investment->grace_period
    //         ? Carbon::parse($investment->investment_date)->addDays($investment->grace_period)
    //         : Carbon::parse($investment->investment_date)->addMonth();

    //     // But actual profit releases only begin from initial_profit_release_month
    //     $firstReleaseDate = Carbon::createFromFormat('M Y', $investment->initial_profit_release_month)
    //         ->startOfMonth();

    //     // Tenure end — counted from schedule start, not release start
    //     $tenureEnd = Carbon::parse($investment->maturity_date)->endOfMonth();

    //     // ---- Step 1: precompute actual release months, starting from firstReleaseDate ----
    //     $releaseMonths = [];
    //     $cursor = $firstReleaseDate->copy();

    //     while ($cursor->lessThanOrEqualTo($tenureEnd)) {
    //         $releaseMonths[$cursor->format('Y-m')] = true;

    //         $cursor = Carbon::parse(calculateNextProfitReleaseDate(
    //             0,
    //             $investment->profit_interval_id,
    //             $cursor->format('M Y'),
    //             $investment->payout_batch_id
    //         ))->startOfMonth();
    //     }


    //     $tenure = $investment->grace_period ? $investment->investment_tenure + 1 : $investment->investment_tenure;

    //     // ---- Step 2: generate one row per month across full tenure, amount only where a release lands ----
    //     for ($i = 0; $i < $tenure; $i++) {
    //         $currentMonth = $scheduleStart->copy()->addMonths($i);
    //         $key = $currentMonth->format('Y-m');

    //         $hasRelease = isset($releaseMonths[$key]);
    //         $profitAmount = $hasRelease ? round($investment->profit_amount_per_interval, 2) : 0;

    //         $row = [
    //             'profit_release_month' => $currentMonth,
    //             'profit_amount'        => $profitAmount,
    //             'has_profit_amount'    => $hasRelease ? 1 : 0,
    //         ];

    //         if ($forInsert) {
    //             $row = array_merge([
    //                 'investor_id'   => $investment->investor_id,
    //                 'investment_id' => $investment->id,
    //             ], $row, [
    //                 'release_status'        => 'pending',
    //                 'released_total_amount' => 0,
    //                 'last_released_at'      => null,
    //                 'last_released_by'      => null,
    //                 'created_at'            => $now,
    //                 'updated_at'            => $now,
    //             ]);
    //         }

    //         $schedule[] = $row;
    //     }


    //     return $schedule;
    // }

    public function updateInvestorProfitRecords(array $profits, $investment): array
    {
        return DB::transaction(function () use ($profits, $investment) {
            return $this->updateOrCreateProfitRecord($profits, $investment);
        });
    }

    public function renewInvestorProfitRecords(array $profitRecords, Investment $investment): array
    {
        return DB::transaction(function () use ($investment, $profitRecords) {
            return $this->updateOrCreateProfitRecord($profitRecords, $investment);
        });
    }

    public function updateOrCreateProfitRecord($profits, $investment)
    {
        $changes = [];
        foreach ($profits as $record) {
            $amount = round((float) ($record['amount'] ?? 0), 2);

            if (empty($record['date'])) {
                continue; // can't create/update without a date
            }

            $releaseMonth = Carbon::createFromFormat('d-m-Y', $record['date'])->startOfDay();

            if (!empty($record['id'])) {
                $existing = InvestmentProfitRecord::where('id', $record['id'])
                    ->where('investment_id', $investment->id)
                    ->first();

                if (!$existing) {
                    continue;
                }

                $existingDate = Carbon::createFromFormat('d-m-Y', $existing->profit_release_month)->startOfDay();

                $dateChanged   = !$existingDate->equalTo($releaseMonth);
                $amountChanged = round((float) $existing->profit_amount, 2) !== $amount;

                if ($dateChanged || $amountChanged) {
                    $old = [
                        'profit_release_month' => $existing->profit_release_month,
                        'profit_amount' => $existing->profit_amount,
                    ];

                    $existing->update([
                        'investor_id'           => $investment->investor_id,
                        'profit_release_month'  => $releaseMonth,
                        'profit_amount'         => $amount,
                        'has_profit_amount'     => $amount > 0 ? 1 : 0,
                        'renewal_count' => $investment->renewal_count,
                        // 'updated_at'            => now(),
                        // 'released_total_amount' => 0,
                        // 'last_released_at'      => null,
                        // 'last_released_by'      => null,
                    ]);


                    $changes[] = [
                        'action' => 'updated',
                        'id' => $existing->id,
                        'old' => $old,
                        'new' => [
                            'profit_release_month' => $releaseMonth->toDateString(),
                            'profit_amount' => $amount,
                        ],
                    ];
                }
            } else {
                // No id - new row added on the frontend - create
                $created = InvestmentProfitRecord::create([
                    'investor_id'           => $investment->investor_id,
                    'investment_id'         => $investment->id,
                    'profit_release_month'  => $releaseMonth,
                    'profit_amount'         => $amount,
                    'renewal_count' => $investment->renewal_count,
                    'has_profit_amount'     => $amount > 0 ? 1 : 0,
                    'release_status'        => 'pending',
                    'released_total_amount' => 0,
                    'last_released_at'      => null,
                    'last_released_by'      => null,
                ]);


                $changes[] = [
                    'action' => 'created',
                    'id' => $created->id,
                    'new' => [
                        'profit_release_month' => $releaseMonth->toDateString(),
                        'profit_amount' => $amount,
                    ],
                ];
            }
        }

        return $changes;
    }


    public function getRenewalQuery(array $filters = []): Builder
    {
        $twoWeeksLater = Carbon::today()->addWeeks(2)->format('Y-m-d');
        $permittedCompanyIds = getUserPermittedCompanyIds(auth()->user()->id, 'investment');

        $query = Investment::with('investor', 'payoutBatch', 'profitInterval', 'company', 'investmentReferral', 'companyAllocations.company', 'investedCompany');

        $query->whereHas('company', function ($q) use ($permittedCompanyIds) {
            $q->whereIn('company_id', $permittedCompanyIds);
        });

        $query->whereDate('maturity_date', '<=', $twoWeeksLater)
            ->where('terminate_status', 0)
            ->where('investment_term_type', 1);
        // ->where('investment_status', 0);

        if (!empty($filters['investor_id'])) {
            $query->where('investor_id', $filters['investor_id']);
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
    //  public function getCompanyTotalInvestmentsQuery($investorId, $cutoffDate = null)
    // {
    //     // dd($investorId, $cutoffDate);
    //     $query = Investment::select(
    //         'investments.company_id',
    //         'companies.company_name as company_name',
    //         DB::raw('SUM(investments.investment_amount) as total_invested')
    //     )
    //         ->join(
    //             'companies',
    //             'companies.id',
    //             '=',
    //             'investments.company_id'
    //         )
    //         ->where('investments.investor_id', $investorId)
    //         ->where('investments.terminate_status', '!=', 2);
    //         // ->where('investments.investment_term_type', 1);

    //     if ($cutoffDate !== null) {
    //         $query->whereDate(
    //             'investments.investment_date',
    //             '<=',
    //             $cutoffDate
    //         );
    //     }


    //     $data = $query
    //         ->groupBy(
    //             'investments.company_id',
    //             'companies.company_name'
    //         )
    //         ->orderBy('companies.company_name')
    //         ->get();

    //     return [
    //         'companies' => $data,
    //         'grand_total' => $data->sum('total_invested'),
    //     ];
    // }
    public function getCompanyTotalInvestmentsQuery($investorId, $cutoffDate = null)
    {
        $query = Investment::select(
            'investments.company_id',
            'companies.company_name as company_name',

            DB::raw('SUM(
                CASE
                    WHEN investments.investment_term_type = 1
                    THEN investments.investment_amount
                    ELSE 0
                END
            ) as short_term_amount'),

            DB::raw('SUM(
                CASE
                    WHEN investments.investment_term_type = 2
                    THEN investments.investment_amount
                    ELSE 0
                END
            ) as long_term_amount'),

            DB::raw('SUM(investments.investment_amount) as total_invested')
        )
            ->join(
                'companies',
                'companies.id',
                '=',
                'investments.company_id'
            )
            ->where('investments.investor_id', $investorId)
            ->where('investments.terminate_status', '!=', 2)
            ->whereIn('investments.investment_term_type', [1, 2]);

        if ($cutoffDate !== null) {
            $query->whereDate(
                'investments.investment_date',
                '<=',
                $cutoffDate
            );
        }

        $data = $query
            ->groupBy(
                'investments.company_id',
                'companies.company_name'
            )
            ->orderBy('companies.company_name')
            ->get();

        return [
            'companies' => $data,
            'short_term_total' => $data->sum('short_term_amount'),
            'long_term_total' => $data->sum('long_term_amount'),
            'grand_total' => $data->sum('total_invested'),
        ];
    }
}
