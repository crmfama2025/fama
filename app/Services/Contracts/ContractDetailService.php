<?php

namespace App\Services\Contracts;

use App\Repositories\Contracts\ContractDetailRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ContractDetailService
{
    public function __construct(
        protected ContractDetailRepository $detailRepo,
    ) {}

    public function getAll()
    {
        return $this->detailRepo->all();
    }

    public function getById($id)
    {
        return $this->detailRepo->find($id);
    }

    public function create($contract_id, array $data, $user_id = null)
    {
        $this->validate($data);
        $data['contract_id'] = $contract_id;
        $data['added_by'] = $user_id ? $user_id : auth()->user()->id;

        return $this->detailRepo->create($data);
    }

    public function update(array $data)
    {
        $id = $data['id'];
        $this->validate($data, $id);
        $data['updated_by'] = auth()->user()->id;
        return $this->detailRepo->update($id, $data);
    }

    private function validate(array $data, $id = null)
    {
        $validator = Validator::make($data, [
            // 'nationality_name' => [
            //     'required',
            //     Rule::unique('nationalities')->ignore($id)
            //         ->where(fn($q) => $q
            //             // ->where('company_id', $data['company_id'])
            //             ->whereNull('deleted_at'))
            // ],
            // 'nationality_short_code' => 'required',
            // 'company_id' => 'required|exists:companies,id',
            'grace_period' => 'required',
            'closing_date' => 'required',
            'start_date' => 'required',
            'duration_in_months' => 'required',
            'end_date' => 'required',

        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
    public function update_detail($id, array $data)
    {
        $data['updated_by'] = auth()->user()->id;
        return $this->detailRepo->update($id, $data);
    }
}
