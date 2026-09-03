<?php

namespace App\Services;

use App\Exports\GenericExport;
use App\Models\Lead;
use App\Models\LeadAssignment;
use App\Models\LeadFollowUp;
use App\Repositories\LeadRepository;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class LeadService
{
    public function __construct(
        protected LeadRepository $leadRepository,
    ) {}


    /**
     * Get all leads.
     */
    public function getAll()
    {
        return $this->leadRepository->all();
    }


    /**
     * Get lead by ID.
     */
    public function getById($id)
    {
        return $this->leadRepository->find($id);
    }


    /**
     * Create or restore lead.
     */
    public function createOrRestore(array $data, $user_id = null)
    {
        $this->validate($data);

        $data['created_by'] = $user_id
            ? $user_id
            : auth()->user()->id;

        $data['status'] = $data['status'] ?? 0;
        $data['lead_code'] = $this->setLeadCode();

        // dd($data);
        return $this->leadRepository->create($data);
    }
    public function setLeadCode($addval = 1)
    {
        $codeService = new \App\Services\CodeGeneratorService();
        return $codeService->generateNextCode('leads', 'lead_code', 'LEA', 5, $addval);
    }

    /**
     * Update lead.
     */
    public function update($id, array $data)
    {
        $this->validate($data, $id);

        $data['updated_by'] = auth()->user()->id;

        return $this->leadRepository->updateOrRestore($id, $data);
    }


    /**
     * Delete lead.
     */
    public function delete($id)
    {
        return DB::transaction(function () use ($id) {
            // Get the lead
            $lead = $this->leadRepository->find($id);

            if (!$lead) {
                throw new \Exception('Lead not found.');
            }

            // Store who deleted the lead
            $lead->deleted_by = auth()->id();

            // Soft delete the lead
            $lead->save();
            $lead->delete();

            return true;
        });
    }


    private function validate(array $data, $id = null)
    {
        $validator = Validator::make($data, [
            'company_name' => ['nullable', 'string', 'max:255'],
            'contact_person_name' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'lead_source' => ['required', 'string', 'max:255'],
            'total_staff' => ['nullable', 'integer', 'min:0'],
            'required_location' => ['nullable', 'string', 'max:255'],
            'requirement' => ['required', 'string'],
            'status' => ['nullable', 'integer', Rule::in([0, 1])],
        ], [
            'contact_person_name.required' => 'Contact person name is required.',
            'phone_number.required' => 'Phone number is required.',
            'email.email' => 'Please enter a valid email address.',
            'lead_source.required' => 'Lead source is required.',
            'total_staff.integer' => 'Total staff must be a number.',
            'requirement.required' => 'Requirement is required.',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }


    /**
     * Get DataTable data.
     */
    public function getDataTable(array $filters = [])
    {
        $user = auth()->user();

        if ((int) $user->user_type_id === 3) {
            $filters['assigned_to'] = $user->id;
        }

        $query = $this->leadRepository->getQuery($filters);

        $columns = [
            ['data' => 'DT_RowIndex', 'name' => 'id'],
            ['data' => 'company_name', 'name' => 'company_name'],
            ['data' => 'contact_person_name', 'name' => 'contact_person_name'],
            ['data' => 'phone_number', 'name' => 'phone_number'],
            ['data' => 'email', 'name' => 'email'],
            ['data' => 'lead_source', 'name' => 'lead_source'],
            ['data' => 'total_staff', 'name' => 'total_staff'],
            ['data' => 'required_location', 'name' => 'required_location'],
            ['data' => 'requirement', 'name' => 'requirement'],
            ['data' => 'status', 'name' => 'status'],
            [
                'data' => 'action',
                'name' => 'action',
                'orderable' => false,
                'searchable' => false,
            ],
        ];

        return datatables()
            ->of($query)
            ->addIndexColumn()
            ->addColumn('company_name', fn($row) => $row->company_name ?? '-')
            ->addColumn('contact_person_name', fn($row) => $row->contact_person_name ?? '-')
            ->addColumn('phone_number', fn($row) => $row->phone_number ?? '-')
            ->addColumn('email', fn($row) => $row->email ?? '-')
            ->addColumn('lead_source', fn($row) => $row->lead_source ?? '-')
            ->addColumn('total_staff', fn($row) => $row->total_staff ?? '-')
            ->addColumn('required_location', fn($row) => $row->required_location ?? '-')
            ->addColumn('requirement', fn($row) => $row->requirement ?? '-')
            ->addColumn('status', function ($row) {
                return $this->getLeadStatus($row->status, true);
            })
            ->addColumn('action', function ($row) {
                $action = '<div class="d-flex flex-column flex-md-row">';

                if (auth()->user()->hasAnyPermission(['leads.view'])) {
                    $action .= '<a href="' . route('lead.show', $row->id) . '" class="btn btn-warning btn-sm mb-1 mr-md-1" title="View Lead" data-toggle="tooltip"><i class="fas fa-eye"></i></a>';
                }

                // Assigned leads cannot be edited or deleted
                if (!$row->assigned_to) {
                    if (auth()->user()->hasAnyPermission(['leads.edit'])) {
                        $action .= '<a href="' . route('lead.edit', $row->id) . '" class="btn btn-info btn-sm mb-1 mr-md-1" title="Edit Lead" data-toggle="tooltip"><i class="fas fa-edit"></i></a>';
                    }

                    if (auth()->user()->hasAnyPermission(['leads.delete'])) {
                        $action .= '<button class="btn btn-danger btn-sm mb-1" onclick="deleteConf(' . $row->id . ')" type="button" title="Delete Lead" data-toggle="tooltip"><i class="fas fa-trash"></i></button>';
                    }
                }

                // Converted lead
                if ((int) $row->status === 9) {
                    $action .= '<a href="' . route('tenant.create', ['lead_id' => $row->id]) . '" class="btn btn-success btn-sm mb-1" title="Create Tenant" data-toggle="tooltip"><i class="fas fa-user-plus"></i></a>';
                }

                return $action . '</div>';
            })
            ->rawColumns(['status', 'action'])
            ->with(['columns' => $columns])
            ->toJson();
    }



    public function getLeadExportData(array $filters = [])
    {
        $query = $this->leadRepository->getQuery($filters);

        return $query->get()->map(function ($row, $index) {
            $id = $index + 1;
            $lead_code = $row->lead_code ?? '-';
            $companyName = $row->company_name ?? '-';
            $contactPerson = $row->contact_person_name ?? '-';
            $phoneNumber = $row->phone_number ?? '-';
            $email = $row->email ?? '-';
            $leadSource = $row->lead_source ?? '-';
            $totalStaff = $row->total_staff ?? '-';
            $requiredLocation = $row->required_location ?? '-';
            $requirement = $row->requirement ?? '-';
            $status = $this->getLeadStatus($row->status);
            $addedBy = $row->createdBy
                ? trim($row->createdBy->first_name . ' ' . $row->createdBy->last_name)
                : '-';
            $updatedBy = $row->updatedBy
                ? trim($row->updatedBy->first_name . ' ' . $row->updatedBy->last_name)
                : '-';
            $createdAt = $row->created_at
                ? Carbon::parse($row->created_at)->format('d-m-Y')
                : '-';
            $updatedAt = $row->updated_at
                ? Carbon::parse($row->updated_at)->format('d-m-Y')
                : '-';
            $assignedTo = $row->assignedTo
                ? trim($row->assignedTo->first_name . ' ' . $row->assignedTo->last_name)
                : '-';

            return [
                $id,
                $lead_code,
                $companyName,
                $contactPerson,
                $phoneNumber,
                $email,
                $leadSource,
                $totalStaff,
                $requiredLocation,
                $requirement,
                $assignedTo,
                $status,
                $addedBy,
                $updatedBy,
                $createdAt,
                $updatedAt,
            ];
        });
    }


    public function leadExportHeadings()
    {
        return [
            '#',
            'Lead Code',
            'Company Name',
            'Contact Person',
            'Phone Number',
            'Email',
            'Lead Source',
            'Total Staff',
            'Required Location',
            'Requirement',
            'Assigned To',
            'Status',
            'Added By',
            'Updated By',
            'Created At',
            'Updated At',
        ];
    }
    public function assignLead($leadId, array $data)
    {
        return DB::transaction(function () use ($leadId, $data) {

            $lead = Lead::findOrFail($leadId);

            $lead->update([
                'assigned_to' => $data['assigned_to'],
                'assigned_by' => auth()->id(),
                'assignment_remarks' => $data['assignment_remarks'] ?? null,
                'assigned_at' => now(),
            ]);

            LeadAssignment::create([
                'lead_id' => $lead->id,
                'assigned_to' => $data['assigned_to'],
                'assigned_by' => auth()->id(),
                'remarks' => $data['assignment_remarks'] ?? null,
                'assigned_at' => now(),
            ]);

            return $lead->fresh();
        });
    }

    public function validateFollowUp(array $data)
    {
        $validator = Validator::make($data, [
            'follow_up_status' => 'required|integer',
            'follow_up_type'   => 'required|integer',
            'follow_up_date'   => 'required|date',

            'not_interested_reason' => 'nullable|string',

            'meeting_date'     => 'nullable|date',
            'meeting_time'     => 'nullable',
            'meeting_location' => 'nullable|string',

            'notes' => 'nullable|string',

            'next_follow_up_date' => 'nullable|date',
            'next_follow_up_time' => 'nullable',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
    public function storeFollowUp($leadId, array $data)
    {
        $data = $this->validateFollowUp($data);

        return DB::transaction(function () use ($leadId, $data) {
            $lead = $this->leadRepository->find($leadId);

            if (!$lead) {
                throw new \Exception('Lead not found.');
            }
            $followUpData = $this->getFollowUpData($data);

            $followUpData['lead_id'] = $lead->id;
            $followUpData['created_by'] = auth()->id();

            $followUp = LeadFollowUp::create($followUpData);
            // $followUp = LeadFollowUp::create([
            //     'lead_id' => $lead->id,
            //     'follow_up_status' => $data['follow_up_status'],
            //     'follow_up_type' => $data['follow_up_type'],
            //     'not_interested_reason' => $data['not_interested_reason'] ?? null,
            //     'meeting_date' => $data['meeting_date'] ?? null,
            //     'meeting_time' => $data['meeting_time'] ?? null,
            //     'meeting_location' => $data['meeting_location'] ?? null,
            //     'notes' => $data['notes'] ?? null,
            //     'next_follow_up_date' => $data['next_follow_up_date'] ?? null,
            //     'next_follow_up_time' => $data['next_follow_up_time'] ?? null,
            //     'follow_up_date' => $data['follow_up_date'] ?? null,
            //     'created_by' => auth()->id(),
            // ]);
            // dd($followUp);

            $lead->status = $data['follow_up_status'];
            $lead->updated_by = auth()->id();
            $lead->save();

            return $followUp;
        });
    }
    public function updateFollowUp($leadId, $followUpId, array $data)
    {
        $data = $this->validateFollowUp($data);

        return DB::transaction(function () use ($leadId, $followUpId, $data) {
            $lead = $this->leadRepository->find($leadId);

            if (!$lead) {
                throw new \Exception('Lead not found.');
            }

            $followUp = LeadFollowUp::where('id', $followUpId)
                ->where('lead_id', $leadId)
                ->first();

            if (!$followUp) {
                throw new \Exception('Follow-up not found.');
            }

            $latestFollowUp = LeadFollowUp::where('lead_id', $leadId)
                ->latest('created_at')
                ->first();

            if (!$latestFollowUp || $latestFollowUp->id != $followUpId) {
                throw new \Exception('Only the latest follow-up can be edited.');
            }

            $updateData = $this->getFollowUpData($data);
            $updateData['updated_by'] = auth()->id();

            $status = (int) $data['follow_up_status'];

            if ($status !== 5) {
                $updateData['not_interested_reason'] = null;
            }

            if ($status !== 6) {
                $updateData['meeting_date'] = null;
                $updateData['meeting_time'] = null;
                $updateData['meeting_location'] = null;
            }

            if (in_array($status, [9, 10])) {
                $updateData['next_follow_up_date'] = null;
                $updateData['next_follow_up_time'] = null;
            }

            $followUp->update($updateData);

            $lead->status = $status;
            $lead->updated_by = auth()->id();
            $lead->save();

            return $followUp;
        });
    }

    public function deleteFollowUp($id)
    {
        return DB::transaction(function () use ($id) {
            // Get follow-up and related lead
            $followUp = LeadFollowUp::findOrFail($id);
            $lead = $followUp->lead;

            // Only assigned salesperson can delete
            if (auth()->id() != $lead->assigned_to) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to delete this follow-up.',
                ], 403);
            }

            // Make sure only the latest follow-up can be deleted
            $latestFollowUpId = LeadFollowUp::where('lead_id', $lead->id)
                ->latest('id')
                ->value('id');

            if ((int) $followUp->id !== (int) $latestFollowUpId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only the latest follow-up can be deleted.',
                ], 422);
            }

            // Store who deleted the follow-up
            $followUp->update([
                'deleted_by' => auth()->id(),
            ]);

            // Soft delete the follow-up
            $followUp->delete();

            // Get the latest remaining follow-up
            $latestFollowUp = LeadFollowUp::where('lead_id', $lead->id)
                ->latest('id')
                ->first();

            if ($latestFollowUp) {
                // Revert lead status to latest remaining follow-up status
                $lead->update([
                    'status' => $latestFollowUp->follow_up_status,
                ]);
            } else {
                // No follow-ups remaining, put lead back to Processing
                $lead->update([
                    'status' => 1,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Follow-up deleted successfully.',
            ]);
        });
    }
    private function getLeadStatus($status, $html = false)
    {
        return match ((int) $status) {
            1 => $html
                ? '<span class="badge bg-info">Processing</span>'
                : 'Processing',

            2 => $html
                ? '<span class="badge bg-success">Interested</span>'
                : 'Interested',

            3 => $html
                ? '<span class="badge bg-primary">Call Back</span>'
                : 'Call Back',

            4 => $html
                ? '<span class="badge bg-secondary">No Answer</span>'
                : 'No Answer',

            5 => $html
                ? '<span class="badge bg-danger">Not Interested</span>'
                : 'Not Interested',

            6 => $html
                ? '<span class="badge bg-purple">Meeting Scheduled</span>'
                : 'Meeting Scheduled',

            7 => $html
                ? '<span class="badge bg-info">Proposal Sent</span>'
                : 'Proposal Sent',

            8 => $html
                ? '<span class="badge bg-warning text-dark">Negotiation</span>'
                : 'Negotiation',

            9 => $html
                ? '<span class="badge bg-success">Converted</span>'
                : 'Converted',

            10 => $html
                ? '<span class="badge bg-danger">Lost</span>'
                : 'Lost',

            11 => $html
                ? '<span class="badge bg-dark">Others</span>'
                : 'Others',

            default => $html
                ? '<span class="badge bg-secondary">Pending</span>'
                : 'Pending',
        };
    }
    private function getFollowUpData(array $data): array
    {
        return [
            'follow_up_status' => $data['follow_up_status'],
            'follow_up_type' => $data['follow_up_type'],
            'follow_up_date' => $data['follow_up_date'] ?? null,
            'not_interested_reason' => $data['not_interested_reason'] ?? null,
            'meeting_date' => $data['meeting_date'] ?? null,
            'meeting_time' => $data['meeting_time'] ?? null,
            'meeting_location' => $data['meeting_location'] ?? null,
            'notes' => $data['notes'] ?? null,
            'next_follow_up_date' => $data['next_follow_up_date'] ?? null,
            'next_follow_up_time' => $data['next_follow_up_time'] ?? null,
        ];
    }
}
