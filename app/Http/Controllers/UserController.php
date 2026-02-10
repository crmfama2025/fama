<?php

namespace App\Http\Controllers;

use App\Exports\UserExport;
use App\Models\Permission;
use App\Models\User;
use App\Models\UserType;
use App\Services\CompanyService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService,
        protected CompanyService $companyService,
    ) {}


    public function index()
    {
        $title = 'Users';

        return view("admin.user.user", compact("title"));
    }

    public function createOrEdit($id = null)
    {
        $title = "Add User";
        $companies = $this->companyService->getAll();
        $user_types = UserType::all();
        $permissions = Permission::with('children')
            ->whereNull('parent_id')
            ->get();

        $subModules = [
            'add',
            'view',
            'edit',
            'delete',
            'approve',
            'reject',
            'document_upload',
            'renew',
            'send_for_approval',
            'sign_after_approval',
            'terminate',
            'invoice_upload',
            'manage_installments',
            'payout',
            'payable_cheque_clearing',
            'receivable_cheque_clearing',
            'submit_pending',
            'soa',
            'referrals',
            'rent_split'
        ];

        if ($id) {
            // Edit mode
            $user = User::with('permissions')->findOrFail($id);

            $userPermissionIds = $user->permissions->pluck('id')->toArray();
        } else {
            // Add mode
            $userPermissionIds = [];
            $user = null;
        }

        return view("admin.user.manage-user", compact("title", "companies", "user_types", "permissions", "userPermissionIds", "subModules", "user"));
    }

    public function store(Request $request)
    {
        try {
            if ($request->id != 0) {
                $user = $this->userService->createUserWithPermissions($request->all(), $request->file('profile_photo'), $request->id);

                return response()->json(['success' => true, 'data' => $user, 'message' => 'User updated successfully'], 200);
            } else {
                $user = $this->userService->createUserWithPermissions($request->all(), $request->file('profile_photo'));

                return response()->json(['success' => true, 'data' => $user, 'message' => 'User created successfully'], 201);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'error'   => $e], 500);
        }
    }

    public function getUsers(Request $request)
    {
        if ($request->ajax()) {
            $filters = [
                'company_id' => auth()->user()->company_id,
                'search' => $request->search['value'] ?? null
            ];
            return $this->userService->getDataTable($filters);
        }
    }

    public function exportUsers(Request $request)
    {
        $search = request('search');
        $filters = auth()->user()->company_id ? [
            'company_id' => auth()->user()->company_id,
        ] : null;

        return Excel::download(new UserExport($search, $filters), 'users.xlsx');
    }

    public function destroy(User $user)
    {
        $this->userService->delete($user->id);
        return response()->json(['success' => true, 'message' => 'User soft deleted']);
    }

    public function userProfile()
    {
        $title = "User Profile";
        $companies = $this->companyService->getAll();
        $user_types = UserType::all();
        $user = User::with('permissions')->findOrFail(auth()->user()->id);

        return view("admin.user.user-profile", compact("title", "companies", "user_types", "user"));
    }
}
