<?php

namespace App\Http\Controllers;

use App\Exports\PropertyExport;
use App\Models\Property;
use App\Models\PropertySizeUnit;
use App\Services\AreaService;
use App\Services\CompanyService;
use App\Services\LocalityService;
use App\Services\PropertyService;
use App\Services\MakaniLocatorService;
use App\Services\PropertyTypeService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class PropertyController extends Controller
{

    public function __construct(
        protected PropertyService $propertyService,
        protected CompanyService $companyService,
        protected LocalityService $localityService,
        protected AreaService $areaService,
        protected PropertyTypeService $propertyTypeService,
        protected MakaniLocatorService $makaniLocatorService,
    ) {}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = 'Property Type';
        $companies = $this->companyService->getAll();
        $localities = $this->localityService->getAll();
        $areas = $this->areaService->getAll();
        $property_types = $this->propertyTypeService->getAll();
        $propertySizeUnits = PropertySizeUnit::all();

        return view("admin.master.property", compact("companies", "localities", "areas", "property_types", "propertySizeUnits"));
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
        // dd($request);
        try {
            if (!empty($request->id)) {
                $property = $this->propertyService->update($request->id, $request->all());
                return response()->json([
                    'success' => true,
                    'data' => $property,
                    'message' => 'Property  updated successfully'
                ], 200);
            } else {
                $property = $this->propertyService->createOrRestore($request->all());

                return response()->json([
                    'success' => true,
                    'data' => $property,
                    'message' => 'Property  created successfully'
                ], 201);
            }
        } catch (\Exception $e) {
            if ($e->getCode() == 23000) { // integrity constraint violation
                throw ValidationException::withMessages([
                    'property' => 'Property already exists for this locality under this company.',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'error'   => $e
                ], 500);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Property $property)
    {
        //
        return view("admin.master.property-view", compact('property'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Property $property)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Property $property)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Property $property)
    {
        $this->propertyService->delete($property->id);
        return response()->json(['success' => true, 'message' => 'Property soft deleted']);
    }

    public function getProperties(Request $request)
    {
        if ($request->ajax()) {
            $filters = [
                'company_id' => auth()->user()->company_id,
                'search' => $request->search['value'] ?? null
            ];

            return $this->propertyService->getDataTable($filters);
        }
    }

    public function exportProperty(Request $request)
    {
        $search = request('search');
        $filters = auth()->user()->company_id ? [
            'company_id' => auth()->user()->company_id,
        ] : null;


        return Excel::download(new PropertyExport($search, $filters), 'property.xlsx');
    }

    public function importProperty(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv'
        ]);

        $file = $request->file('file');

        // Pass a second argument as required by importExcel, e.g., the current user ID or null if not needed
        $result = $this->propertyService->importExcel($file, auth()->user()->id);

        // return redirect()->back()->with('success', "$count property imported successfully.");
        if ($result['inserted'] == 0 && $result['restored'] == 0) {
            return response()->json(['success' => false, 'message' => "No new property to import."]);
        } else {
            return response()->json(['success' => true, 'message' => "{$result['inserted']} created, {$result['restored']} restored successfully."]);
        }
    }
    public function makaniLocate(Request $request, MakaniLocatorService $locator)
    {
        $data = $request->validate([
            'makani_number' => 'required|string',
            'location_link' => 'required|url',
        ]);

        return response()->json($this->compareMakani($data, $locator));
    }


    public function makaniVerify(Request $request, MakaniLocatorService $locator)
    {
        $data = $request->validate([
            'area_id'        => 'required|exists:areas,id',
            'locality_id'    => 'required|exists:localities,id',
            'building_name'  => 'required|string|max:255',
            'plot_no'        => 'nullable|string|max:50',
            'makani_number'  => 'required|string',
            'location_link'  => 'required|url',
            'confirm_update' => 'nullable|boolean',
        ]);

        $makani          = preg_replace('/\D/', '', $data['makani_number']);
        $makaniFormatted = substr($makani, 0, 5) . ' ' . substr($makani, 5);
        $name            = trim($data['building_name']);
        $plot            = trim($data['plot_no'] ?? '');

        // Rule 0: Makani unique everywhere
        $used = Property::with(['area', 'locality'])
            ->whereRaw("REPLACE(makani_number, ' ', '') = ?", [$makani])
            ->first();
        if ($used) {
            return $this->makaniFail(
                "Makani number already used by \"{$used->property_name}\" ({$used->area?->area_name} / {$used->locality?->locality_name})."
            );
        }

        // Same building (area + locality + name)
        $matches = Property::with(['area', 'locality'])
            ->where('area_id', $data['area_id'])
            ->where('locality_id', $data['locality_id'])
            ->whereRaw('LOWER(TRIM(property_name)) = ?', [mb_strtolower($name)])
            ->get();

        $existing = null;   // row to update
        $refId    = null;   // old property to reference
        $plotToSave = $plot !== '' ? $plot : '0';

        if ($plot !== '') {
            $exact = $matches->first(
                fn($p) => mb_strtolower(trim((string) $p->plot_no)) === mb_strtolower($plot)
            );

            if ($exact) {
                if (filled($exact->makani_number)) {
                    return $this->makaniFail("This property already exists with Makani number {$exact->makani_number}.");
                }
                $existing = $exact;                      // same plot, no Makani -> update
            } else {
                // no exact plot match: row with empty plot and empty Makani -> update it and set the plot
                $blankRow = $matches->first(
                    fn($p) => in_array(trim((string) $p->plot_no), ['', '0'], true) && blank($p->makani_number)
                );

                if ($blankRow) {
                    $existing = $blankRow;
                }
                // else: create new with this plot
            }
        } else {
            // ---- no plot ----
            // if ($matches->isNotEmpty()) {
            //     if ($matches->contains(fn($p) => filled($p->makani_number))) {
            //         return $this->makaniFail('This building already has a Makani number in this area and locality.');
            //     }
            //     if ($matches->count() > 1) {
            //         return $this->makaniFail('This building has multiple plots. Please enter the plot no.');
            //     }
            //     $refId = $matches->first()->id;   // create new + reference old
            // }
            if ($matches->isNotEmpty()) {

                // building already has a Makani -> reject
                if ($matches->contains(fn($p) => filled($p->makani_number))) {
                    return $this->makaniFail('This building already has a Makani number in this area and locality.');
                }

                // row with empty plot and empty Makani -> update it
                $blankRow = $matches->first(
                    fn($p) => in_array(trim((string) $p->plot_no), ['', '0'], true) && blank($p->makani_number)
                );

                if ($blankRow) {
                    $existing = $blankRow;
                } else {
                    // only rows that have plot numbers -> create new + reference old
                    if ($matches->count() > 1) {
                        return $this->makaniFail('This building has multiple plots. Please enter the plot no.');
                    }
                    $refId = $matches->first()->id;
                }
            }
        }

        // Distance check
        $r = $this->compareMakani($data, $locator);
        if (!$r['within']) {
            return $this->makaniFail("Makani location and map link are {$r['distance']} m apart (max 100 m).");
        }

        $makaniFields = [
            'makani_number'      => $makaniFormatted,
            'makani_status'      => 'Approved',
            'makani_latitude'    => $r['makani']['lat'],
            'makani_longitude'   => $r['makani']['lng'],
            'makani_distance_m'  => $r['distance'],
            'makani_verified_at' => now(),
            'makani_verified_by' => auth()->id(),
        ];

        // Update existing (plot matched, no Makani yet)
        if ($existing) {
            if (!$request->boolean('confirm_update')) {
                return response()->json([
                    'success'            => false,
                    'needs_confirmation' => true,
                    'message'            => 'This property already exists without a Makani number.',
                    'existing'           => [
                        'id'            => $existing->id,
                        'property_name' => $existing->property_name,
                        'area'          => $existing->area?->area_name,
                        'locality'      => $existing->locality?->locality_name,
                        'plot_no'       => $existing->plot_no,
                        'property_size' => $existing->property_size,
                        'makani_number' => $existing->makani_number,
                    ],
                ], 409);
            }

            $existing->update($makaniFields + [
                'plot_no'    => $plot !== '' ? $plot : $existing->plot_no,
                'location'   => $existing->location  ?: $data['location_link'],
                'latitude'   => $existing->latitude  ?: $r['link']['lat'],
                'longitude'  => $existing->longitude ?: $r['link']['lng'],
                'updated_by' => auth()->id(),
            ]);

            return response()->json(['success' => true, 'message' => 'Existing property updated with Makani details.', 'data' => $existing]);
        }

        // Create new (with or without reference)
        try {
            $property = $this->propertyService->createOrRestore($makaniFields + [
                'company_id'       => auth()->user()->company_id,
                'area_id'          => $data['area_id'],
                'locality_id'      => $data['locality_id'],
                'property_type_id' => null,
                'property_name'    => $name,
                'plot_no'          => $plotToSave,
                'location'         => $data['location_link'],
                'latitude'         => $r['link']['lat'],
                'longitude'        => $r['link']['lng'],
                'status'           => 1,
                'source'           => 2,
                'ref_property_id'  => $refId,
            ], auth()->id());
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) {
                return $this->makaniFail('Property already exists for this locality.');
            }
            throw $e;
        }

        return response()->json(['success' => true, 'message' => 'Makani verified and property added.', 'data' => $property]);
    }

    // private function compareMakani(array $data, MakaniLocatorService $locator): array
    // {
    //     $makani = preg_replace('/\D/', '', $data['makani_number']);

    //     if (strlen($makani) !== 10) {
    //         throw ValidationException::withMessages(['makani_number' => 'Makani number must be 10 digits.']);
    //     }

    //     $m = $locator->fromMakani($makani);
    //     $l = $locator->fromLink($data['location_link']);
    //     $distance = (int) round(MakaniLocatorService::distanceMeters($m['lat'], $m['lng'], $l['lat'], $l['lng']));

    //     return ['makani' => $m, 'link' => $l, 'distance' => $distance, 'within' => $distance <= 100];
    // }
    private function compareMakani(array $data, MakaniLocatorService $locator): array
    {
        $makani = preg_replace('/\D/', '', $data['makani_number']);

        if (strlen($makani) !== 10) {
            throw ValidationException::withMessages([
                'makani_number' => 'Makani number must be 10 digits.'
            ]);
        }

        // Get coordinates using Makani number
        $m = $locator->fromMakani($makani);

        // Get coordinates using Google Maps link
        $l = $locator->fromLink($data['location_link']);

        // Compare both locations
        $distance = (int) round(
            MakaniLocatorService::distanceMeters(
                $m['lat'],
                $m['lng'],
                $l['lat'],
                $l['lng']
            )
        );

        return [
            'makani' => $m,
            'link' => $l,
            'distance' => $distance,
            'within' => $distance <= 100,
        ];
    }

    private function makaniFail(string $msg)
    {
        return response()->json(['success' => false, 'message' => $msg], 422);
    }
}
