<?php

namespace App\Repositories\Contracts;

use App\Models\ContractPayableClear;
use App\Models\ContractPaymentDetail;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class PayableClearRepository
{
    public function all()
    {
        return ContractPayableClear::all();
    }

    public function find($id)
    {
        return ContractPayableClear::findOrFail($id);
    }

    public function getByCondition($contractPaymentDet)
    {
        return ContractPayableClear::where($contractPaymentDet)->first();
    }

    public function create($data)
    {
        return ContractPayableClear::create($data);
    }

    public function createMany(array $dataArray)
    {
        $detId = [];
        foreach ($dataArray as $data) {
            $detId[] = ContractPayableClear::create($data);
        }
        return  $detId;
    }

    public function updateMany(array $data)
    {
        $detId = [];
        foreach ($data as $key => $value) {
            $paymentdet = $this->find($key);
            $paymentdet->update($value);

            $detId[] = $key;
        }
        return  $detId;
    }

    public function delete($id)
    {
        $pDet = $this->find($id);
        $pDet->deleted_by = auth()->user()->id;
        $pDet->save();
        return $pDet->delete();
    }

    public function getPayables(array $filters = []): Builder
    {
        $twoWeeksLater = now()->addDays(14)->toDateString();

        $query = ContractPaymentDetail::query()
            ->with([
                'contract',
                'contract.vendor',
                'contract.property',
                'contract.company',
                'contract.contract_type',
                'payment_mode',
                'bank',
                'payables'
            ]);

        // ->with('payables')
        // ->select(
        //     'contract_payment_details.*',
        //     'contracts.project_number',
        //     'companies.company_name',
        //     'vendors.vendor_name',
        //     'properties.property_name',
        //     'contract_types.contract_type',
        //     'contract_types.id as contract_type_id'
        // )
        // ->join('contracts', 'contract_payment_details.contract_id', '=', 'contracts.id')
        // ->join('vendors', 'vendors.id', '=', 'contracts.vendor_id')
        // ->join('properties', 'properties.id', '=', 'contracts.property_id')
        // ->join('payment_modes', 'payment_modes.id', '=', 'contract_payment_details.payment_mode_id')
        // ->join('companies', 'companies.id', '=', 'contracts.company_id')
        // ->join('contract_types', 'contract_types.id', '=', 'contracts.contract_type_id')
        $query->whereHas('contract', function ($q) {
            $q->where('contract_status', 7);
        })
            ->where('paid_status', '!=', 1);


        $todate = '';
        $fromDate = '';
        if (empty($filters['filter']) || empty($filters['filter']['date_to'])) {
            $todate = $twoWeeksLater;
        }



        if (!empty($filters['filter'])) {
            $filter = $filters['filter'];

            // Vendor filter
            if ($filter['vendor_id']) {
                $query->whereHas('contract', function ($q) use ($filter) {
                    $q->where('vendor_id', $filter['vendor_id']);
                });
            }

            // property filter
            if ($filter['property_id']) {
                $query->whereHas('contract', function ($q) use ($filter) {
                    $q->where('property_id', $filter['property_id']);
                });
            }

            // payment mode filter
            if ($filter['payment_mode']) {
                $query->whereHas('payment_mode', function ($q) use ($filter) {
                    $q->where('payment_mode_id', $filter['payment_mode']);
                });
            }

            // // Date range
            // if ($filters['date_from'] || $filters['date_to']) {
            //     $query->whereBetween('contract_payment_details.payment_date', [
            //         $filters['date_from'],
            //         $filters['date_to']
            //     ]);
            // }

            if (!empty($filter['date_from'])) {
                $fromDate = $filter['date_from'];
            }

            if (!empty($filter['date_to'])) {
                $todate = $filter['date_to'];
            }
        }

        if ($fromDate) {
            $query->whereBetween('payment_date', [
                $fromDate,
                $todate
            ]);
        } else {
            $query->where(
                'payment_date',
                '<=',
                $todate
            );
        }


        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $searchLike = str_replace('-', '%', $search);

            $query->where(function ($q) use ($search, $searchLike) {
                $q->whereRaw('payment_date LIKE ?', ["%{$searchLike}%"])
                    ->orWhereRaw("CAST(payment_amount AS CHAR) LIKE ?", ["%{$search}%"])


                    ->orWhereHas('contract', function ($q) use ($search) {
                        $q->orwhere('project_code', 'like', '%' . $search . '%')
                            ->orWhere('project_number', 'like', '%' . $search . '%');
                    })

                    ->orWhereHas('contract.company', function ($q) use ($search) {
                        $q->where('company_name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('contract.contract_type', function ($q) use ($search) {
                        $q->where('contract_type', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('contract.property', function ($q) use ($search) {
                        $q->where('property_name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('contract.vendor', function ($q) use ($search) {
                        $q->where('vendor_name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('payment_mode', function ($q) use ($search) {
                        $q->where('payment_mode_name', 'like', '%' . $search . '%');
                    });
            });
        }


        // if (!empty($filters['company_id'])) {
        //     $query->Where('contracts.company_id', $filters['company_id']);
        // }


        return $query;
    }

    public function getClearedData(array $filters = []): Builder
    {
        $fromDate = now()->startOfMonth()->toDateString();
        $todate = now()->toDateString();

        $query = ContractPayableClear::query()
            ->with([
                'contractPaymentDetail',
                'contract',
                'contract.vendor',
                'contract.property',
                'contract.company',
                'contract.contract_type',
                'paidMode',
                'paidBank'
            ]);


        if (!empty($filters['filter'])) {
            $filter = $filters['filter'];


            if (!empty($filter['date_from'])) {
                $fromDate = $filter['date_from'];
            }

            if (!empty($filter['date_to'])) {
                $todate = $filter['date_to'];
            }
        }

        $query->whereBetween('contract_payable_clears.paid_date', [
            $fromDate,
            $todate
        ]);

        // $query2 = DB::query()->fromSub($query, 'x');
        // dump(!empty($filters['search']));
        if (!empty($filters['search'])) {

            $search = trim($filters['search']);
            $searchLike = str_replace('-', '%', $search);

            $query->where(function ($q) use ($search, $searchLike) {
                // $q->orWhere('company_name', 'LIKE', "%{$search}%")
                //     ->orWhere('contract_type', 'LIKE', "%{$search}%")
                //     ->orWhereRaw("CAST(paid_amount AS CHAR) LIKE ?", ["%{$search}%"])
                //     ->orWhereRaw("CAST(pending_amount AS CHAR) LIKE ?", ["%{$search}%"])
                //     ->orWhere('project_code', 'LIKE', "%{$search}%")
                //     ->orWhere('project_number', 'LIKE', "%{$search}%")
                //     ->orWhere('property_name', 'LIKE', "%{$search}%")
                //     ->orWhere('vendor_name', 'LIKE', "%{$search}%")
                //     ->orWhere('payment_mode_name', 'LIKE', "%{$search}%")
                //     ->orWhereRaw('payment_date LIKE ?', ["%{$searchLike}%"])
                //     ->orWhereRaw('paid_date LIKE ?', ["%{$searchLike}%"])
                //     ->orWhereRaw('CAST(id AS CHAR) LIKE ?', ["%{$search}%"])
                //     ->orWhereRaw("CONCAT(
                //                     ROW_NUMBER() OVER (PARTITION BY contract_id ORDER BY id),
                //                     '/',
                //                     COUNT(*) OVER (PARTITION BY contract_id)
                //                 ) LIKE ?", ["%{$search}%"]);

                $q->whereHas('contractPaymentDetail', function ($q) use ($search) {
                    $q->where('payment_date', 'like', "%{$search}%");
                    // dump('hiii');
                })
                    ->orWhereHas('contract', function ($q) use ($search) {
                        $q->where('project_number', 'like', "%{$search}%")
                            ->orWhere('project_code', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('contract.company', function ($q) use ($search) {
                        $q->where('company_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('contract.vendor', function ($q) use ($search) {
                        $q->where('vendor_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('contract.contract_type', function ($q) use ($search) {
                        $q->where('contract_type', 'like', "%{$search}%");
                    })
                    ->orWhereHas('contract.locality', function ($q) use ($search) {
                        $q->where('locality_name',  'like', "%{$search}%");
                    })
                    ->orWhereHas('contract.property', function ($q) use ($search) {
                        $q->where('property_name',  'like', "%{$search}%");
                    })
                    ->orWhereHas('paidMode', function ($q) use ($search) {
                        $q->where('payment_mode_name',  'like', "%{$search}%");
                    })
                    ->orWhereRaw('paid_date LIKE ?', ["%{$searchLike}%"])
                    ->orWhereRaw("CAST(paid_amount AS CHAR) LIKE ?", ["%{$search}%"])
                    ->orWhereRaw("CAST(pending_amount AS CHAR) LIKE ?", ["%{$search}%"]);
            });
        }


        // if (!empty($filters['company_id'])) {
        //     $query->Where('contracts.company_id', $filters['company_id']);
        // }
        // dd($query);
        return $query;
    }
}
