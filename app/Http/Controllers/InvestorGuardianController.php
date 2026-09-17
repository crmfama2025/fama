<?php

namespace App\Http\Controllers;

use App\Repositories\Investment\InvestorGuardianRepository;
use App\Services\Investment\InvestorGuardianService;
use Illuminate\Http\Request;

class InvestorGuardianController extends Controller
{
    //
    public function __construct(
        protected InvestorGuardianService $guardianService,
        protected InvestorGuardianRepository $investorGuardianRepo,


    ) {}
    public function index()
    {
        $title = "Investor Guardians";
        return view('admin.investment.guardians.index', compact('title'));
    }
    public function getData(Request $request)
    {
        if ($request->ajax()) {

            $filters =  [
                'search' => is_array($request->search)
                    ? ($request->search['value'] ?? null)
                    : $request->search,
                // 'status' => $request->follow_up_status,
                // 'lead_source' => $request->lead_source,
                // 'follow_up_date_from' => $request->follow_up_date_from,
                // 'follow_up_date_to' => $request->follow_up_date_to,
                // 'next_follow_up_from' => $request->next_follow_up_from,
                // 'next_follow_up_to' => $request->next_follow_up_to,
                // 'assigned_to' => $request->followed_up_by,

            ];

            return $this->guardianService->getDataTable($filters);
        }
    }

    public function show($id)
    {
        $investorGuardian = $this->investorGuardianRepo->find($id);
        $investorGuardian->load('investors');
        // if (!$investorGuardian) {
        //     return redirect()
        //         ->route('investor.guardian.list')
        //         ->with('error', 'Guardian not found.');
        // }

        return view('admin.investment.guardians.view', [
            'title' => 'Guardian Detail',
            'investorGuardian' => $investorGuardian,
        ]);
    }
    public function store(Request $request)
    {
        // dd($request->all());
        try {
            $guardian = $this->guardianService->createGuardian($request->all());
            return response()->json(['success' => true, 'guardian' => [
                'id' => $guardian->id,
                'guardian_name' => $guardian->guardian_name,
                'investor_guardian_code' => $guardian->investor_guardian_code,
            ], 'message' => 'Guardian created successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'error'   => $e], 500);
        }
    }

    public function edit($id)
    {
        $guardian = $this->investorGuardianRepo->find($id);

        if (!$guardian) {
            return response()->json([
                'success' => false,
                'message' => 'Guardian not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'guardian' => [
                'id' => $guardian->id,
                'guardian_name' => $guardian->guardian_name,
                'guardian_name_arabic' => $guardian->guardian_name_arabic,
                'guardian_mobile' => $guardian->guardian_mobile,
                'guardian_email' => $guardian->guardian_email,
                'emirates_id_number' => $guardian->emirates_id_number,
                'eid_expiry_date' => $guardian->eid_expiry_date,
                'passport_number' => $guardian->passport_number,
                'passport_expiry_date' => $guardian->passport_expiry_date,
                'emirates_id_copy' => $guardian->emirates_id_copy
                    ? asset('storage/' . $guardian->emirates_id_copy)
                    : null,
                'passport_copy' => $guardian->passport_copy
                    ? asset('storage/' . $guardian->passport_copy)
                    : null,
                'guardian_address' => $guardian->guardian_address ?? null,
                'guardian_address_ar' => $guardian->guardian_address_ar ?? null,
            ]
        ]);
    }
    public function update($id, Request $request)
    {
        try {
            $guardian = $this->guardianService->updateGuardian($id, $request->all());
            return response()->json(['success' => true, 'guardian' => [
                'id' => $guardian->id,
                'guardian_name' => $guardian->guardian_name,
                'investor_guardian_code' => $guardian->investorguardian_code,
            ], 'message' => 'Guardian updated successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'error'   => $e], 500);
        }
    }

    public function destroy($id)
    {
        $this->guardianService->delete($id);
        return response()->json(['success' => true, 'message' => 'Investor deleted successfully']);
    }
}
