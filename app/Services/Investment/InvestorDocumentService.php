<?php

namespace App\Services\Investment;

use App\Repositories\Investment\InvestorBankRepository;
use App\Repositories\Investment\InvestorDocumentRepository;
use App\Repositories\Investment\InvestorRepository;
use App\Services\PdfCompressionService;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class InvestorDocumentService
{
    public function __construct(
        protected InvestorDocumentRepository $investorDocRepo,
        protected InvestorRepository $investorRepo,
    ) {}

    public function getAll()
    {
        return $this->investorDocRepo->all();
    }

    public function getAllActive()
    {
        return $this->investorDocRepo->allActive();
    }

    public function getById($id)
    {
        return $this->investorDocRepo->find($id);
    }

    public function getByName($name)
    {
        return $this->investorDocRepo->getByName($name);
    }

    public function getByInvestor($data)
    {
        return $this->investorDocRepo->getByInvestor($data);
    }

    public function create(array $data, $investor = null)
    {
        $dataArr = $this->documentArr($data, $investor);

        return $this->investorDocRepo->createMany($dataArr);
    }

    public function update(array $data, $investor = null)
    {
        $dataArr = $this->documentArr($data, $investor);
        $data['updated_by'] = auth()->user()->id;
        return $this->investorDocRepo->updateMany($dataArr);
    }

    public function documentArr($data, $investor)
    {
        $dataArr = [];
        foreach ($data as $value) {

            $documentExist = $this->getByName(['document_type_id' => $value['document_type_id'], 'investor_id' => $investor->id]);

            if ($value['document_type_id'] == 4 && empty($documentExist)) {
                // dump(isset($value['file']));
                if (!isset($value['file'])) {
                    throw ValidationException::withMessages([
                        'file' => 'Emirates ID / Other ID file is required',
                    ]);
                }
            }

            if (isset($value["file"])) {
                $filename = time() . '_' . $value["file"]->getClientOriginalName();
                // $path = $value["file"]->storeAs('investments/' . $investor->investor_code . '/investor', $filename, 'public');

                $pdfService = new PdfCompressionService();

                if ($value["file"]->getClientOriginalExtension() === 'pdf') {
                    $path = $pdfService->compress(
                        $value["file"],
                        'investments/' . $investor->investor_code . '/investor',
                        $filename
                    );
                } else {
                    $path = $value["file"]->storeAs(
                        'investments/' . $investor->investor_code . '/investor',
                        $filename,
                        'public'
                    );
                }

                $Arr = array(
                    'investor_id' => $investor->id,
                    'document_type_id' => $value['document_type_id'],
                    'document_name' => $filename,
                    'document_path' => $path,
                );

                if ($documentExist) {
                    $Arr['doc_id'] = $documentExist->id;
                    $Arr['updated_by'] = auth()->user()->id;
                } else {
                    $Arr['added_by'] = auth()->user()->id;
                }

                $this->investorFlagUpdate($investor->id, $value['status_change']);
                // $this->validate($Arr);
                $dataArr[] = $Arr;
            }
        }

        return $dataArr;
    }

    public function investorFlagUpdate($investorId, $flag)
    {
        $investor = $this->investorRepo->find($investorId);
        $investor->{$flag} = 1;
        $investor->save();
    }

    public function delete($id)
    {
        return $this->investorDocRepo->delete($id);
    }

    public function setInvestorCode($addval = 1)
    {
        $codeService = new \App\Services\CodeGeneratorService();
        return $codeService->generateNextCode('investors', 'investor_code', 'INVR', 5, $addval);
    }

    private function validate(array $data, $id = null)
    {
        // $validator = Validator::make($data, [
        //     'file' => 'required|file',
        // ], [
        //     'file.required' => 'Emirates ID / Other ID file is required',
        // ]);

        // if ($validator->fails()) {
        //     throw new ValidationException($validator);
        // }
    }

    // public function getDataTable(array $filters = [])
    // {
    //     $query = $this->investorDocRepo->getQuery($filters);

    //     $columns = [
    //         ['data' => 'DT_RowIndex', 'name' => 'id'],
    //         ['data' => 'area_name', 'name' => 'area_name'],
    //         ['data' => 'company_name', 'name' => 'company_name'],
    //         ['data' => 'action', 'name' => 'action', 'orderable' => true, 'searchable' => true],
    //     ];

    //     return datatables()
    //         ->of($query)
    //         ->addIndexColumn()
    //         ->addColumn('area_name', fn($row) => $row->area_name ?? '-')
    //         ->addColumn('company_name', fn($row) => $row->company->company_name ?? '-')
    //         ->addColumn('action', function ($row) {
    //             $action = '';
    //             if (Gate::allows('area.edit')) {
    //                 $action .= '<button class="btn btn-info" data-toggle="modal"
    //                                                     data-target="#modal-area" data-id="' . $row->id . '"
    //                                                     data-name="' . $row->area_name . '"
    //                                                     data-company="' . $row->company_id . '">Edit</button>';
    //             }
    //             if (Gate::allows('area.delete')) {
    //                 $action .= '<button class="btn btn-danger ml-1" onclick="deleteConf(' . $row->id . ')" type="submit">Delete</button>';
    //             }

    //             return $action ?: '-';
    //         })
    //         ->rawColumns(['action'])
    //         ->with(['columns' => $columns]) // send columns too
    //         ->toJson();
    // }
}
