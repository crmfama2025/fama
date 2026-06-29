<?php

namespace App\Repositories\Investment;

use App\Models\InvestorAgreementTemplate;
use Illuminate\Contracts\Database\Eloquent\Builder;

class InvestorAgreementRepository
{
    public function __construct(
        protected InvestorAgreementTemplate $model
    ) {}

    public function findById($id)
    {
        return $this->model->find($id);
    }

    public function findByType($docTypeId)
    {
        return $this->model
            ->where('investor_agreement_type_id', $docTypeId)
            ->orderBy('version_no', 'desc')
            ->first();
    }

    public function create(array $data)
    {
        return InvestorAgreementTemplate::create($data);
    }

    public function update(int $id, array $data)
    {
        $investor = InvestorAgreementTemplate::findOrFail($id);
        $investor->update($data);

        return $investor;
    }

    public function getQuery(array $filters = []): Builder
    {
        $query = InvestorAgreementTemplate::query()
            ->with(['agreementType'])           // Eager Loading
            ->select('investor_agreement_templates.*');

        if (!empty($filters['search'])) {
            $search = '%' . $filters['search'] . '%';

            $query->where(function ($q) use ($search) {
                $q->where('version_no', 'like', $search)
                    ->orWhere('effective_from', 'like', $search)
                    ->orWhereHas('agreementType', function ($sub) use ($search) {
                        $sub->where('investor_agreement_type', 'like', $search);
                    });
            });
        }

        return $query;
    }
    public function getActiveIdBytype($tdocTpeId)
    {

        $template = InvestorAgreementTemplate::where('investor_agreement_type_id', $tdocTpeId)->where('is_active', 1)->first();
        return $template->id;
    }
}
