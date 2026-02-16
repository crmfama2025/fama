<?php

namespace App\Services;

use App\Imports\VendorImport;
use App\Repositories\PermissionRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class UserService
{

    public function __construct(
        protected UserRepository $userRepository,
        protected PermissionRepository $permissionRepository,
        protected CompanyService $companyService,
    ) {}

    public function getAll()
    {
        return $this->userRepository->all();
    }

    public function getById($id)
    {
        return $this->userRepository->find($id);
    }

    public function createOrRestore(array $data, $user_id = null)
    {
        $this->validate($data);
        $data['added_by'] = $user_id ? $user_id : auth()->user()->id;
        $data['user_code'] = $this->setUserCode();

        $existing = $this->userRepository->checkIfExist($data);

        if ($existing) {
            if ($existing->trashed()) {
                $existing->restore();
            }
            $existing->fill($data);
            $existing->save();
            return $existing;
        }

        return $this->userRepository->create($data);
    }

    public function createUserWithPermissions(array $data, $file = null, $user_id = null)
    {
        $userData = $data;
        $user_id = $userData['id'] ?? null;

        if ($file) {
            if ($user_id) {
                $existingUser = $this->userRepository->find($user_id);
                if ($existingUser && $existingUser->profile_photo && Storage::disk('public')->exists($existingUser->profile_path)) {
                    Storage::disk('public')->delete($existingUser->profile_path);
                }
            }
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('uploads/profile_photos', $filename, 'public');
            $userData['profile_photo'] = $filename;
            $userData['profile_path'] = $path;
        }

        $this->validate($data, $user_id);
        // $permissionIds = $data['permission_id'] ?? null;
        if (!empty($userData['password'])) {
            $userData['password'] =  bcrypt($userData['password']);
        } else {
            unset($userData['password']);
        }

        return DB::transaction(function () use ($userData, $user_id) { // $permissionIds,
            // Step 1: Create User

            if ($user_id == null) {
                $userData['user_code'] = $this->setUserCode();
                $userData['added_by'] = $user_id ? $user_id : auth()->user()->id;
            } else {
                $userData['updated_by'] = $user_id ? $user_id : auth()->user()->id;
            }


            if ($user_id) {
                $user = $this->userRepository->updateOrRestore($user_id, $userData);
            } else {
                $user = $this->userRepository->create($userData);
            }


            // Step 2: Assign Permissions
            // if (!empty($permissionIds)) {
            //     $this->permissionRepository->assignToUser($user, $permissionIds);
            // }

            return $user;
        });
    }

    // public function update($id, array $data)
    // {
    //     $this->validate($data, $id);
    //     $data['updated_by'] = auth()->user()->id;
    //     return $this->userRepository->updateOrRestore($id, $data);
    // }

    public function delete($id)
    {
        return $this->userRepository->delete($id);
    }

    public function setUserCode($addval = 1)
    {
        $codeService = new \App\Services\CodeGeneratorService();
        return $codeService->generateNextCode('users', 'user_code', 'USR', 5, $addval);
    }

    private function validate(array $data, $id = 0)
    {
        DB::enableQueryLog();

        $validator = Validator::make($data, [
            // 'email' => 'required|email|unique:users,email',
            'first_name' => 'required',
            'phone' => [
                'required',
                'numeric',
                'regex:/^[1-9][0-9]{9,14}$/',
                Rule::unique('users', 'phone')
                    ->ignore($id)
                    ->where(
                        fn($q) =>
                        $q->whereNull('deleted_at')
                        // ->where('company_id', $data['company_id'])

                    ),
            ],
            'password' => 'required',
            'user_type_id' => 'required',
            'company_id' => 'required|exists:companies,id',
            'username' => [
                'required',
                Rule::unique('users', 'username')->ignore($id)
                    ->where(
                        fn($q) =>
                        $q->whereNull('deleted_at')
                        // ->where('company_id', $data['company_id'])
                    )
            ],
            'email' => [
                'required',
                Rule::unique('users', 'email')
                    ->ignore($id)
                    ->where(
                        fn($q) =>
                        $q->whereNull('deleted_at')
                        // ->where('company_id', $data['company_id'])

                    ),
            ],
            // 'permission_id' => $data['profile'] ? '' : 'required|array|min:1',
            'password' => $id ? 'nullable|string|min:6' : 'required|string|min:6',
            // // 'permission_id.*' => 'integer|exists:permissions,id',

        ], [
            'email.unique' => 'This email already exists for this company.',
            'username.unique' => 'This username already exists for this company.',
            'company_id.required' => 'Please select Company.',
            'user_type_id.required' => 'Please select a User Type.',
            // 'permission_id.required' => 'Please provide at least one permission.',
        ]);

        if ($validator->fails()) {
            // dd($validator->errors());
            // dd($validator->errors(), DB::getQueryLog());
            throw new ValidationException($validator);
        }
    }

    public function checkIfExist($data)
    {
        return $this->userRepository->checkIfExist($data);
    }

    public function getDataTable(array $filters = [])
    {
        $query = $this->userRepository->getQuery($filters);

        $columns = [
            ['data' => 'DT_RowIndex', 'name' => 'id'],
            ['data' => 'profile_photo', 'name' => 'profile_photo'],
            ['data' => 'company_name', 'name' => 'company_name'],
            ['data' => 'first_name', 'name' => 'first_name'],
            ['data' => 'last_name', 'last_name' => 'last_name'],
            ['data' => 'email', 'name' => 'email'],
            ['data' => 'phone', 'name' => 'phone'],
            ['data' => 'username', 'name' => 'username'],
            ['data' => 'user_type', 'name' => 'user_type'],
            ['data' => 'action', 'name' => 'action', 'orderable' => true, 'searchable' => true],
        ];

        return datatables()
            ->of($query)
            ->addIndexColumn()
            ->addColumn('profile_photo', fn($row) => $row->profile_path
                ? '<img src="' . asset('storage/' . $row->profile_path) . '" alt="' . $row->profile_photo . '" class="rounded-circle" width="50">'
                : '-')
            ->addColumn('company_name', fn($row) => $row->company->company_name ?? '-')
            ->addColumn('full_name', fn($row) => $row->first_name . ' ' . $row->last_name ?? '-')
            ->addColumn('email', fn($row) => $row->email ?? '-')
            ->addColumn('phone', fn($row) => $row->phone ?? '-')
            ->addColumn('username', fn($row) => $row->username ?? '-')
            ->addColumn('user_type', fn($row) => $row->user_type ?? '-')
            ->addColumn('action', function ($row) {
                $action = '<div class="d-flex flex-column flex-md-row ">';
                if (Gate::allows('user.edit')) {
                    $action .= '<a class="btn btn-info mb-1 mr-md-1" href="' . route('user.createoredit', $row->id) . '">Edit</a>';
                }
                if (Gate::allows('user.delete')) {
                    $action .= '<button class="btn btn-danger mb-1" onclick="deleteConf(' . $row->id . ')" type="submit">Delete</button>';
                }

                if (Gate::allows('user.edit') || Gate::allows('user.add')) {
                    $action .= '<a class="btn btn-secondary mb-1 mt-1 mt-md-0 ml-md-1" href="' . route('user.managePermission', $row->id) . '">Permission</a>';
                }
                $action .= '</div>';

                return $action ?: '-';
            })
            ->rawColumns(['profile_photo', 'action'])
            ->with(['columns' => $columns]) // send columns too
            ->toJson();
    }
}
