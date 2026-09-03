<?php

namespace App\Repositories;

use App\Models\Lead;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class LeadRepository
{
    /**
     * Get all active leads.
     */
    public function all()
    {
        return Lead::where('status', 1)->get();
    }


    /**
     * Find lead by ID.
     */
    public function find($id)
    {
        return Lead::findOrFail($id);
    }


    /**
     * Get lead by given data.
     */
    public function getByData($leadData)
    {
        return Lead::where($leadData)->first();
    }


    /**
     * Create a new lead.
     */
    public function create($data)
    {
        return Lead::create($data);
    }


    /**
     * Update or restore a lead.
     */
    public function updateOrRestore(int $id, array $data)
    {
        $lead = Lead::findOrFail($id);


        $lead->update($data);

        return $lead;
    }


    /**
     * Soft delete a lead.
     */
    public function delete($id)
    {
        $lead = $this->find($id);

        return $lead->delete();
    }


    /**
     * Get leads by phone number.
     */
    public function getByPhone($phoneNumber)
    {
        return Lead::where('phone_number', $phoneNumber)->first();
    }


    /**
     * Get leads by company name.
     */
    public function getByCompany($companyName)
    {
        return Lead::where('company_name', $companyName)->get();
    }


    /**
     * Get DataTable query.
     */
    public function getQuery(array $filters = []): Builder
    {
        $query = Lead::query()->select('leads.*');

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', '%' . $search . '%')
                    ->orWhere('contact_person_name', 'like', '%' . $search . '%')
                    ->orWhere('phone_number', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('lead_source', 'like', '%' . $search . '%')
                    ->orWhere('required_location', 'like', '%' . $search . '%')
                    ->orWhere('requirement', 'like', '%' . $search . '%')
                    ->orWhereRaw("CAST(leads.id AS CHAR) LIKE ?", ['%' . $search . '%']);
            });
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['lead_source'])) {
            $query->where('lead_source', $filters['lead_source']);
        }

        if (!empty($filters['required_location'])) {
            $query->where('required_location', 'like', '%' . $filters['required_location'] . '%');
        }

        if (!empty($filters['assigned_to'])) {
            $query->where('assigned_to', $filters['assigned_to']);
        }

        if (
            !empty($filters['follow_up_date_from']) ||
            !empty($filters['follow_up_date_to']) ||
            !empty($filters['next_follow_up_from']) ||
            !empty($filters['next_follow_up_to'])
        ) {
            $query->whereHas('followUps', function ($q) use ($filters) {

                if (!empty($filters['follow_up_date_from'])) {
                    $date = Carbon::createFromFormat('d-m-Y', $filters['follow_up_date_from'])
                        ->format('Y-m-d');
                    $q->whereDate('follow_up_date', '>=', $date);
                }
                if (!empty($filters['follow_up_date_to'])) {
                    $date = Carbon::createFromFormat('d-m-Y', $filters['follow_up_date_to'])
                        ->format('Y-m-d');
                    $q->whereDate('follow_up_date', '<=', $date);
                }
                if (!empty($filters['next_follow_up_from'])) {
                    $date = Carbon::createFromFormat('d-m-Y', $filters['next_follow_up_from'])
                        ->format('Y-m-d');
                    $q->whereDate('next_follow_up_date', '>=', $date);
                }
                if (!empty($filters['next_follow_up_to'])) {
                    $date = Carbon::createFromFormat('d-m-Y', $filters['next_follow_up_to'])
                        ->format('Y-m-d');
                    $q->whereDate('next_follow_up_date', '<=', $date);
                }
            });
        }

        return $query;
    }


    /**
     * Check if lead already exists.
     *
     * Phone number is used as the primary duplicate check.
     */
    public function checkIfExist($data)
    {
        return Lead::withTrashed()
            ->where(
                'phone_number',
                trim($data['phone_number'])
            )
            ->first();
    }
}
