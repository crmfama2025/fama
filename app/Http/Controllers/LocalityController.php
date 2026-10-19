<?php

namespace App\Http\Controllers;

use App\Exports\LocalityExport;
use App\Models\Area;
use App\Models\Locality;
use App\Models\UnitType;
use App\Models\LocalityPricing;
use App\Services\CompanyService;
use App\Services\LocalityService;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class LocalityController extends Controller
{

    public function __construct(
        protected CompanyService $companyService,
        protected LocalityService $localityService,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = 'Locality';
        $areas = Area::all();
        $companies = $this->companyService->getAll();
        $unitTypes = UnitType::all();
        // dd($unitTypes);
        return view("admin.master.locality", compact("companies", "areas", "unitTypes"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            if ($request->id != 0) {
                $locality = $this->localityService->update($request->id, $request->all());

                return response()->json(['success' => true, 'data' => $locality, 'message' => 'Locality updated successfully'], 200);
            } else {
                $locality = $this->localityService->createOrRestore($request->all());

                return response()->json(['success' => true, 'data' => $locality, 'message' => 'Locality created successfully'], 201);
            }
        } catch (\Exception $e) {
            if ($e->getCode() == 23000) { // integrity constraint violation
                throw ValidationException::withMessages([
                    'property_type' => 'This locality already exists for this company and area.',
                ]);
            } else {
                return response()->json(['success' => false, 'message' => $e->getMessage(), 'error'   => $e], 500);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Locality $locality)
    {
        //
        return view('admin.master.locality-view', compact('locality'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Locality $locality)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Locality $locality)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Locality $locality)
    {
        $this->localityService->delete($locality->id);
        return response()->json(['success' => true, 'message' => 'Locality soft deleted']);
    }

    public function export()
    {
        $search = request('search');
        $filters = auth()->user()->company_id ? [
            'company_id' => auth()->user()->company_id,
        ] : null;

        return Excel::download(new LocalityExport($search, $filters), 'localities.xlsx');
    }

    public function getLocalities(Request $request)
    {
        if ($request->ajax()) {
            $filters = [
                'company_id' => auth()->user()->company_id,
                'search' => $request->search['value'] ?? null
            ];
            return $this->localityService->getDataTable($filters);
        }
    }
    public function getPricing($locality)
    {
        $locality = Locality::with('area')->findOrFail($locality);

        $prices = LocalityPricing::where('locality_id', $locality->id)
            ->get()
            ->keyBy('unit_type_id')
            ->map(fn ($item) => $item->price);

        return response()->json([
            'locality_id' => $locality->id,
            'locality_name' => $locality->locality_name,
            'area_name' => $locality->area->area_name ?? '',
            'prices' => $prices,
        ]);
    }
    public function storePricing(Request $request)
    {
        
        $validated = $request->validate([
            'locality_id' => ['required', 'integer', 'exists:localities,id'],
            'prices' => ['required', 'array'],
            'prices.*.unit_type_id' => [
                'required',
                'integer',
                'distinct',
                'exists:unit_types,id',
            ],
            'prices.*.price' => ['nullable', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['prices'] as $item) {
                $price = $item['price'] ?? null;

                if ($price === '') {
                    $price = null;
                }

                LocalityPricing::updateOrCreate(
                    [
                        'locality_id' => $validated['locality_id'],
                        'unit_type_id' => $item['unit_type_id'],
                    ],
                    [
                        'price' => $price,
                    ]
                );
            }
        });
        return response()->json([
            'message' => 'Locality pricing saved successfully.',
        ]);
    }


    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv'
        ]);

        $file = $request->file('file');

        // Pass a second argument as required by importExcel, e.g., the current user ID or null if not needed
        $result = $this->localityService->importExcel($file, auth()->user()->id);

        // return redirect()->back()->with('success', "$count localities imported successfully.");
        if ($result['inserted'] == 0 && $result['restored'] == 0) {
            return response()->json(['success' => false, 'message' => "No new locality to import."]);
        } else {
            return response()->json(['success' => true, 'message' => "{$result['inserted']} created, {$result['restored']} restored localities imported successfully."]);
        }
    }
}
