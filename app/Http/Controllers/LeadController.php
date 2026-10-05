<?php

namespace App\Http\Controllers;

use App\Exports\GenericExport;
use App\Models\LeadFollowUp;
use App\Services\LeadService;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LeadController extends Controller
{
    //
    public function __construct(
        protected LeadService $leadService,
        protected UserService $userService


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
    public function show($id)
    {
        $title = "Lead Details";

        $lead = $this->leadService->getById($id);
        $salesPerson = $this->userService->getUserByType(3);
        $allocations = $lead->salesAgreement()
            ->orderBy('created_at', 'asc')
            ->get();

        $user = auth()->user();

        $isAssignedSalesPerson =
            (int) $user->user_type_id === 3 &&
            (int) $lead->assigned_to === (int) $user->id;

        // If assigned salesperson is viewing the lead
        if ($isAssignedSalesPerson) {

            // Change Pending -> Processing
            if ((int) $lead->status === 0) {
                $lead->update([
                    'status' => 1,
                    // 'updated_by' => $user->id,
                ]);

                $lead->refresh();
            }

            // Salesperson-specific view
            return view(
                'admin.leads.sales-view',
                compact('title', 'lead', 'allocations')
            );
        }

        return view('admin.leads.view-lead', compact('title', 'lead', 'salesPerson', 'allocations'));
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
}
