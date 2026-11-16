<?php

namespace App\Http\Controllers;

use App\Exports\GenericExport;
use App\Models\LeadFollowUp;
use App\Services\LeadService;
use App\Models\Lead;
use App\Services\UserService;
use App\Services\LeadNotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LeadController extends Controller
{
    //
    public function __construct(
        protected LeadService $leadService,
        protected UserService $userService,
        protected LeadNotificationService $leadNotifier,
    ) {}


    public function index()
    {
        $title = "Leads";

        $salesPersons = $this->userService->getUserByType(3);

        return view('admin.leads.index', compact('title', 'salesPersons'));
    }

    /**
     * Show the form for creating a new lead.
     */
    public function create()
    {
        $title = "Create Lead";

        return view('admin.leads.create-lead', compact('title'));
    }

    public function edit($id)
    {
        $title = "Edit Lead";

        $lead = $this->leadService->getById($id);


        return view('admin.leads.create-lead', compact('title', 'lead'));
    }
    public function store(Request $request)
    {
        try {

            if (!empty($request->id)) {

                $lead = $this->leadService->update(
                    $request->id,
                    $request->all()
                );

                return response()->json([
                    'success' => true,
                    'data' => $lead,
                    'message' => 'Lead updated successfully'
                ], 200);
            } else {

                $lead = $this->leadService->createOrRestore(
                    $request->all()
                );

                return response()->json([
                    'success' => true,
                    'data' => $lead,
                    'message' => 'Lead created successfully'
                ], 201);
            }
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => $e
            ], 500);
        }
    }
    public function getData(Request $request)
    {
        if ($request->ajax()) {

            $filters = $this->getFilters($request);

            return $this->leadService->getDataTable($filters);
        }
    }
    // public function show($id)
    // {
    //     $title = "Lead Details";

    //     $lead = $this->leadService->getById($id);
    //     $user = auth()->user();

    //     // alex: approved leads only
    //     // if ($user->hasAnyPermission(['leads.approved_list']) && (int) $lead->approval_status !== 1) {
    //     //     abort(403, 'This lead is not approved yet.');
    //     // }
    //     if (
    //         $user->hasAnyPermission(['leads.approved_list'])
    //         && !in_array((int) $lead->approval_status, [1, 3, 4], true)
    //     ) {
    //         abort(403, 'This lead is not available for your review yet.');
    //     }
    //     $salesPerson = $this->userService->getUserByType(3);
    //     $allocations = $lead->salesAgreement()
    //         ->orderBy('created_at', 'asc')
    //         ->get();

    //     $user = auth()->user();

    //     $isAssignedSalesPerson =
    //         (int) $user->user_type_id === 3 &&
    //         (int) $lead->assigned_to === (int) $user->id;

    //     // If assigned salesperson is viewing the lead
    //     if ($isAssignedSalesPerson) {

    //         // Change Pending -> Processing
    //         if ((int) $lead->status === 0) {
    //             $lead->update([
    //                 'status' => 1,
    //                 // 'updated_by' => $user->id,
    //             ]);

    //             $lead->refresh();
    //         }

    //         // Salesperson-specific view
    //         return view(
    //             'admin.leads.sales-view',
    //             compact('title', 'lead', 'allocations')
    //         );
    //     }

    //     return view('admin.leads.view-lead', compact('title', 'lead', 'salesPerson', 'allocations'));
    // }
    public function show($id)
    {
        $title = "Lead Details";
        $user  = auth()->user();
        $lead  = $this->leadService->getById($id);

        $isSuperAdmin = (int) $user->user_type_id === 1;
        $isAssignedSalesPerson = (int) $user->user_type_id === 3
            && (int) $lead->assigned_to === (int) $user->id;

        if (!$isSuperAdmin) {
            if ($user->hasAnyPermission(['leads.approve', 'leads.reject'])) {
                // manager: no extra restriction (adjust to match the list)
            } elseif ($user->hasAnyPermission(['leads.approved_list'])) {
                abort_unless(
                    in_array((int) $lead->approval_status, [1, 3, 4], true)
                        && (int) $lead->verifier_id === (int) $user->id
                        && (int) $lead->status !== 0,
                    403,
                    'This lead is not available for your review yet.'
                );
            } else {
                abort_unless(
                    (int) $lead->created_by === (int) $user->id || $isAssignedSalesPerson,
                    403
                );
            }
        }

        $allocations = $lead->salesAgreement()->orderBy('created_at')->get();

        if ($isAssignedSalesPerson) {
            if ((int) $lead->status === 0) {
                $lead->update(['status' => 1]);
            }
            return view('admin.leads.sales-view', compact('title', 'lead', 'allocations'));
        }

        $salesPerson = $this->userService->getUserByType(3);

        return view('admin.leads.view-lead', compact('title', 'lead', 'salesPerson', 'allocations'));
    }
    public function approve(Request $request, $id)
    {
        abort_unless(auth()->user()->hasAnyPermission(['leads.approve']), 403);

        $lead = $this->leadService->getById($id);

        // 0 = pending
        if ((int) $lead->approval_status !== 0) {
            return response()->json([
                'success' => false,
                'message' => 'This lead has already been reviewed.',
            ], 422);
        }

        $validated = $request->validate([
            'verifier_id' => ['nullable', 'exists:users,id'],
            'remarks'     => ['nullable', 'string', 'max:1000'],
        ]);

        $lead->update([
            'approval_status'  => 1, // approved
            'approved_by'      => auth()->id(),
            'approved_at'      => now(),
            'verifier_id'      => $validated['verifier_id'] ?? Lead::DEFAULT_VERIFIER_ID,
            'approval_remarks' => $validated['remarks'] ?? null,
            'rejection_reason' => null,
            'updated_by'       => auth()->id(),
        ]);
        $lead->refresh();
        $this->leadNotifier->leadApproved($lead);

        return response()->json([
            'success' => true,
            'message' => 'Lead approved successfully.',
        ]);
    }
    public function reject(Request $request, $id)
    {
        abort_unless(auth()->user()->hasAnyPermission(['leads.reject']), 403);

        $lead = $this->leadService->getById($id);

        // 0 = pending
        if ((int) $lead->approval_status !== 0) {
            return response()->json([
                'success' => false,
                'message' => 'This lead has already been reviewed.',
            ], 422);
        }

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $lead->update([
            'approval_status'  => 2, // rejected
            'approved_by'      => auth()->id(), // reviewer, reused for rejection
            'approved_at'      => now(),
            'rejection_reason' => $validated['rejection_reason'],
            'updated_by'       => auth()->id(),
        ]);
        $lead->refresh();
        $this->leadNotifier->leadRejected($lead);

        return response()->json([
            'success' => true,
            'message' => 'Lead rejected.',
        ]);
    }
    public function addComment(Request $request, Lead $lead)
    {
        $user = auth()->user();

        abort_unless($user->hasAnyPermission(['leads.comment_add']), 403);

        // Same visibility rules as the list
        $isManager = $user->hasAnyPermission(['leads.approve', 'leads.reject']);
        $isAlex    = $user->hasAnyPermission(['leads.approved_list']);

        if (!$isManager && !$isAlex && (int) $lead->created_by !== (int) $user->id) {
            abort(403);
        }

        // Comments only after approval
        if (!in_array((int) $lead->approval_status, [1, 3, 4], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Comments can be added only after the lead is approved.',
            ], 422);
        }

        $data = $request->validate([
            'comment'         => ['required', 'string', 'max:2000'],
            'approval_status' => ['nullable', 'in:3,4'],
        ]);

        $this->leadService->addComment($lead, $data);

        return response()->json([
            'success' => true,
            'message' => 'Comment added successfully.',
        ]);
    }
    public function export(Request $request)
    {
        try {

            $filters = $this->getFilters($request);

            $data = $this->leadService->getLeadExportData($filters);

            return Excel::download(
                new GenericExport(
                    $data,
                    $this->leadService->leadExportHeadings(),
                    [],       // Date columns
                    ['D'],    // Phone Number
                    []        // Amount columns
                ),
                'leads.xlsx'
            );
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function assign(Request $request, $id)
    {
        $data = $request->all();

        $this->leadService->assignLead(
            $id,
            $data
        );

        return response()->json([
            'success' => true,
            'message' => 'Lead assigned successfully.',
        ]);
    }
    public function storeFollowUp(Request $request, $id)
    {
        $this->leadService->storeFollowUp(
            $id,
            $request->all()
        );

        return response()->json([
            'success' => true,
            'message' => 'Follow-up added successfully.',
        ]);
    }
    public function updateFollowUp(Request $request, $leadId, $followUpId)
    {
        $this->leadService->updateFollowUp(
            $leadId,
            $followUpId,
            $request->all()
        );

        return response()->json([
            'success' => true,
            'message' => 'Follow-up updated successfully.',
        ]);
    }
    public function destroyFollowup($id)
    {
        $this->leadService->deleteFollowUp($id);

        return response()->json([
            'success' => true,
            'message' => 'Follow-up deleted successfully.',
        ]);
    }
    public function destroy($id)
    {
        try {
            $this->leadService->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Lead deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    private function getFilters(Request $request): array
    {
        return [
            'search' => is_array($request->search)
                ? ($request->search['value'] ?? null)
                : $request->search,
            'status' => $request->follow_up_status,
            'lead_source' => $request->lead_source,
            'follow_up_date_from' => $request->follow_up_date_from,
            'follow_up_date_to' => $request->follow_up_date_to,
            'next_follow_up_from' => $request->next_follow_up_from,
            'next_follow_up_to' => $request->next_follow_up_to,
            'assigned_to' => $request->followed_up_by,

        ];
    }

    public function calendarEvents(Request $request)
    {
        $request->validate([
            'start' => ['required', 'date'],
            'end' => ['required', 'date'],
            // 'assigned_to' => ['nullable', 'integer'],
        ]);
        $user = auth()->user();
        $latestFollowUpIds = LeadFollowUp::query()
            ->selectRaw('MAX(id)')
            ->groupBy('lead_id');

        $followUps = LeadFollowUp::with(['lead', 'followedUpBy'])
            ->whereIn('id', $latestFollowUpIds)
            ->whereNotNull('next_follow_up_date')
            ->whereBetween('next_follow_up_date', [
                Carbon::parse($request->start),
                Carbon::parse($request->end),
            ])
            ->whereHas('lead', function ($query) use ($request, $user,) {
                $query->whereNotIn('status', [5, 10, 9]);
                $query->where(function ($q) use ($user) {
                    $q->where('assigned_to', $user->id)
                        ->orWhere('created_by', $user->id);
                });
            })


            ->get();

        return $followUps->map(function ($followUp) {
            $date = Carbon::parse($followUp->next_follow_up_date)->format('Y-m-d');
            $time = $followUp->next_follow_up_time;
            $leadName = $followUp->lead?->company_name ?? 'Lead #' . $followUp->lead_id;
            $start = $time
                ? $date . 'T' . $time
                : $date;

            return [
                'id' => $followUp->id,
                'title' => $leadName,
                'start' => $start,
                'extendedProps' => [
                    'assignedTo' => $followUp->lead->assignedTo
                        ? trim($followUp->lead->assignedTo->first_name . ' ' . $followUp->lead->assignedTo->last_name)
                        : 'Unassigned',
                    'type' => $followUp->follow_up_type_name,
                    'status' => $followUp->follow_up_status,
                    'notes' => $followUp->notes,
                ],
            ];
        });
    }
    // public function locations(Request $request)
    // {
    //     $term = $request->get('q', '');
    //     $items = \App\Models\Locality::query()
    //         ->when($term, fn ($q) => $q->where('locality_name', 'like', "%{$term}%"))
    //         ->orderBy('locality_name')
    //         ->limit(20)
    //         ->get(['id', 'locality_name']);

    //     return response()->json([
    //         'results' => $items->map(fn ($l) => [
    //             'id'   => $l->locality_name,   
    //             'text' => $l->locality_name,
    //         ]),
    //     ]);
    // }
    public function locations(Request $request)
    {
        $term = $request->get('q', '');

        $items = \App\Models\Locality::query()
            ->when($term, fn($q) => $q->where('locality_name', 'like', "%{$term}%"))
            ->when(!$request->boolean('all'), fn($q) => $q->limit(20))
            ->select('locality_name')
            ->distinct()
            ->orderBy('locality_name')
            ->get();

        return response()->json([
            'results' => $items->map(fn($l) => [
                'id'   => $l->locality_name,
                'text' => $l->locality_name,
            ]),
        ]);
    }
    public function updateVerification(Request $request, Lead $lead)
    {
        abort_unless(auth()->user()->hasAnyPermission(['leads.approved_list']), 403);

        $data = $request->validate([
            'approval_status'          => ['required', 'in:3,4'],
            'verification_description' => ['required', 'string', 'max:5000'],
        ]);

        $this->leadService->updateVerification($lead, $data);

        return response()->json([
            'success' => true,
            'message' => 'Verification updated successfully.',
        ]);
    }
}
