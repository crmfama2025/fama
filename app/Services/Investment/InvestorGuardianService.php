<?php

namespace App\Services\Investment;

use App\Models\InvestorGuardianDetail;
use App\Repositories\Investment\InvestorGuardianRepository;
use App\Services\PdfCompressionService;
use Illuminate\Validation\Rule;
use Storage;
use Validator;

class InvestorGuardianService
{
    public function __construct(
        protected InvestorGuardianRepository $investorGuardianRepo,

    ) {}



    public function getDataTable(array $filters = [])
    {
        $query = $this->investorGuardianRepo->getQuery($filters);

        $columns = [
            ['data' => 'DT_RowIndex', 'name' => 'id'],
            ['data' => 'investor_guardian_code', 'name' => 'investor_guardian_code'],
            ['data' => 'guardian_name', 'name' => 'guardian_name'],
            ['data' => 'guardian_name_arabic', 'name' => 'guardian_name_arabic'],
            ['data' => 'guardian_mobile', 'name' => 'guardian_mobile'],
            ['data' => 'guardian_email', 'name' => 'guardian_email'],
            ['data' => 'emirates_id_number', 'name' => 'emirates_id_number'],
            ['data' => 'eid_expiry_date', 'name' => 'eid_expiry_date'],
            ['data' => 'passport_number', 'name' => 'passport_number'],
            ['data' => 'passport_expiry_date', 'name' => 'passport_expiry_date'],
            ['data' => 'added_by', 'name' => 'added_by'],
            ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false],
        ];

        return datatables()
            ->of($query)
            ->addIndexColumn()

            ->addColumn('investor_guardian_code', function ($row) {
                return $row->investor_guardian_code ?? '-';
            })

            ->addColumn('guardian_name', function ($row) {
                $name = $row->guardian_name ?? '-';
                $arabicName = $row->guardian_name_arabic ?? '-';

                return '<strong class="text-capitalize">' . e($name) . '</strong>
                <p class="mb-0 text-muted">' . e($arabicName) . '</p>';
            })

            ->addColumn('guardian_name_arabic', function ($row) {
                return $row->guardian_name_arabic ?? '-';
            })

            ->addColumn('guardian_mobile', function ($row) {
                if (empty($row->guardian_mobile)) {
                    return '-';
                }

                return '<i class="fa fa-phone-alt text-danger mr-1"></i> '
                    . e($row->guardian_mobile);
            })

            ->addColumn('guardian_email', function ($row) {
                if (empty($row->guardian_email)) {
                    return '-';
                }

                return '<span class="text-primary">' . e($row->guardian_email) . '</span>';
            })

            ->addColumn('emirates_id_number', function ($row) {
                return $row->emirates_id_number ?? '-';
            })

            ->addColumn('eid_expiry_date', function ($row) {
                return $row->eid_expiry_date
                    ? \Carbon\Carbon::parse($row->eid_expiry_date)->format('d-m-Y')
                    : '-';
            })

            ->addColumn('passport_number', function ($row) {
                return $row->passport_number ?? '-';
            })

            ->addColumn('passport_expiry_date', function ($row) {
                return $row->passport_expiry_date
                    ? \Carbon\Carbon::parse($row->passport_expiry_date)->format('d-m-Y')
                    : '-';
            })

            ->addColumn('added_by', function ($row) {
                if (!$row->addedBy) {
                    return '-';
                }

                $name = trim(
                    ($row->addedBy->first_name ?? '') . ' ' .
                        ($row->addedBy->last_name ?? '')
                );

                $image = $row->addedBy->profile_path
                    ? asset('storage/' . $row->addedBy->profile_path)
                    : asset('images/default-avatar.png');

                return '
                <div style="display:flex; align-items:center; gap:8px;">
                    <img src="' . $image . '"
                         style="width:30px; height:30px; border-radius:50%; object-fit:cover;">
                    <span>' . e($name ?: '-') . '</span>
                </div>
            ';
            })

            ->addColumn('action', function ($row) {
                $action = '';

                if (auth()->user()->hasAnyPermission(['investor-guardian.edit'])) {
                    $action .= '
                    <button type="button"
                            class="btn btn-info btn-sm editGuardian"
                            data-id="' . $row->id . '"
                            data-toggle="modal"
                            data-target="#addGuardianModal"
                            title="Edit Guardian">
                        <i class="fas fa-pencil-alt"></i>
                    </button>
                ';
                }

                if (auth()->user()->hasAnyPermission(['investor-guardian.view'])) {
                    $action .= '
                    <a href="' . route('investor-guardian.show', $row->id) . '"
                       class="btn btn-primary btn-sm"
                       title="View Guardian">
                        <i class="fas fa-eye"></i>
                    </a>
                ';
                }

                if ($row->investors->isEmpty() && auth()->user()->hasAnyPermission(['investor-guardian.delete'])) {
                    $action .= '
                    <button class="btn btn-danger btn-sm"
                            data-id="' . $row->id . '"
                            onclick="deleteConf(' . $row->id . ')"
                            title="Delete Guardian">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                ';
                }

                return $action ?: '-';
            })

            ->rawColumns([
                'guardian_name',
                'guardian_mobile',
                'guardian_email',
                'added_by',
                'action'
            ])
            ->with(['columns' => $columns])
            ->toJson();
    }
    public function setInvestorGuardianCode($addval = 1)
    {
        $codeService = new \App\Services\CodeGeneratorService();
        return $codeService->generateNextCode('investor_guardian_details', 'investor_guardian_code', 'INVGN', 5, $addval);
    }
    public function createGuardian($guardianArr)
    {
        // $investor = $this->investorRepo->find($investorId);

        // $guardianArr['investor_id'] = $investorId;
        // dd($guardianArr, gettype($guardianArr));
        $this->validate($guardianArr);
        $guardianArr['investor_guardian_code'] = $this->setInvestorGuardianCode();
        $guardianArr['added_by'] = auth()->id();
        $guardianArr['eid_expiry_date'] = parseDate($guardianArr['eid_expiry_date'] ?? null);
        $guardianArr['passport_expiry_date'] = parseDate($guardianArr['passport_expiry_date'] ?? null);
        $pdfService = new PdfCompressionService();

        if (
            isset($guardianArr['emirates_id_copy']) &&
            $guardianArr['emirates_id_copy'] instanceof \Illuminate\Http\UploadedFile
        ) {
            $file = $guardianArr['emirates_id_copy'];

            $filename = time() . '_emirates_id.' . $file->getClientOriginalExtension();

            if (strtolower($file->getClientOriginalExtension()) === 'pdf') {
                $guardianArr['emirates_id_copy'] = $pdfService->compress(
                    $file,
                    'investments/guardians/' .  $guardianArr['investor_guardian_code'],
                    $filename
                );
            } else {
                $guardianArr['emirates_id_copy'] = $file->storeAs(
                    'investments/guardians/' .  $guardianArr['investor_guardian_code'],
                    $filename,
                    'public'
                );
            }
        }

        if (
            isset($guardianArr['passport_copy']) &&
            $guardianArr['passport_copy'] instanceof \Illuminate\Http\UploadedFile
        ) {
            $file = $guardianArr['passport_copy'];

            $filename = time() . '_passport.' . $file->getClientOriginalExtension();

            if (strtolower($file->getClientOriginalExtension()) === 'pdf') {
                $guardianArr['passport_copy'] = $pdfService->compress(
                    $file,
                    'investments/guardians/' . $guardianArr['investor_guardian_code'],
                    $filename
                );
            } else {
                $guardianArr['passport_copy'] = $file->storeAs(
                    'investments/guardians/' . $guardianArr['investor_guardian_code'],
                    $filename,
                    'public'
                );
            }
        }

        return $this->investorGuardianRepo->createGuardian($guardianArr);
    }

    public function updateGuardian($id, $guardianArr)
    {
        $this->validate($guardianArr, $id);
        $guardian = InvestorGuardianDetail::findOrFail($id);

        $guardianArr['eid_expiry_date'] = parseDate($guardianArr['eid_expiry_date'] ?? null);
        $guardianArr['passport_expiry_date'] = parseDate($guardianArr['passport_expiry_date'] ?? null);

        $pdfService = new PdfCompressionService();

        $folder = 'investments/guardians/' . $guardian->investor_guardian_code;

        if (
            isset($guardianArr['emirates_id_copy']) &&
            $guardianArr['emirates_id_copy'] instanceof \Illuminate\Http\UploadedFile
        ) {
            $file = $guardianArr['emirates_id_copy'];

            if ($guardian->emirates_id_copy) {
                Storage::disk('public')->delete($guardian->emirates_id_copy);
            }

            $filename = time() . '_emirates_id.' . $file->getClientOriginalExtension();

            if (strtolower($file->getClientOriginalExtension()) === 'pdf') {
                $guardianArr['emirates_id_copy'] = $pdfService->compress(
                    $file,
                    $folder,
                    $filename
                );
            } else {
                $guardianArr['emirates_id_copy'] = $file->storeAs(
                    $folder,
                    $filename,
                    'public'
                );
            }
        } else {
            unset($guardianArr['emirates_id_copy']);
        }

        if (
            isset($guardianArr['passport_copy']) &&
            $guardianArr['passport_copy'] instanceof \Illuminate\Http\UploadedFile
        ) {
            $file = $guardianArr['passport_copy'];

            if ($guardian->passport_copy) {
                Storage::disk('public')->delete($guardian->passport_copy);
            }

            $filename = time() . '_passport.' . $file->getClientOriginalExtension();

            if (strtolower($file->getClientOriginalExtension()) === 'pdf') {
                $guardianArr['passport_copy'] = $pdfService->compress(
                    $file,
                    $folder,
                    $filename
                );
            } else {
                $guardianArr['passport_copy'] = $file->storeAs(
                    $folder,
                    $filename,
                    'public'
                );
            }
        } else {
            unset($guardianArr['passport_copy']);
        }

        return $this->investorGuardianRepo->updateGuardian(
            $id,
            $guardianArr
        );
    }
    public function delete($id)
    {
        return $this->investorGuardianRepo->delete($id);
    }
    public function validate($data, $id = null)
    {
        return Validator::make(
            $data,
            [
                'guardian_name' => 'required|string|max:255',
                'guardian_name_arabic' => 'nullable|string|max:255',
                'guardian_mobile' => 'required|string|max:20',
                'guardian_email' => 'nullable|email|max:255',
                'emirates_id_number' =>
                ['required', 'string', Rule::unique('investor_guardian_details', 'emirates_id_number')->ignore($id),],
                'eid_expiry_date' => 'required|date',
                'passport_number' => ['required', 'string', Rule::unique('investor_guardian_details', 'passport_number')->ignore($id),],
                'passport_expiry_date' => 'required|date',
                'emirates_id_copy' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
                'passport_copy' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            ]
        )->validate();
    }
}
