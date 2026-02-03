@extends('admin.layout.admin_master')

@section('content')
    <div class="content-wrapper">

        {{-- Page Header --}}
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Property Detail</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('property.index') }}">Properties</a></li>
                            <li class="breadcrumb-item active">View</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        {{-- Main Content --}}
        <section class="content">
            <div class="card">

                {{-- Card Header --}}
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-building mr-1 text-blue"></i>
                        {{ $property->property_name }}
                    </h3>

                    <div class="card-tools">
                        <a href="{{ route('property.index') }}" class="btn btn-sm btn-warning">
                            <i class="fa-arrow-alt-circle-left fas"></i> Back
                        </a>
                    </div>
                </div>

                {{-- Card Body --}}
                <div class="card-body">
                    <div class="row">

                        {{-- LEFT SIDE --}}
                        <div class="col-lg-8">

                            {{-- Info Boxes --}}
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="info-box bg-light">
                                        <div class="info-box-content text-center">
                                            <span class="info-box-text text-muted">Property Code</span>
                                            <span class="info-box-number">{{ $property->property_code }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="info-box bg-light">
                                        <div class="info-box-content text-center">
                                            <span class="info-box-text text-muted">Property Size</span>
                                            <span class="info-box-number">
                                                {{ $property->property_size }}
                                                {{ $property->propertySizeUnit->unit_name ?? ' ' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="info-box bg-light">
                                        <div class="info-box-content text-center">
                                            <span class="info-box-text text-muted">Status</span>
                                            <span class="badge badge-{{ $property->status ? 'success' : 'secondary' }}">
                                                {{ $property->status ? 'Active' : 'Inactive' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Property Details --}}
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h3 class="card-title">Property Information</h3>
                                </div>

                                <div class="card-body">
                                    <table class="table table-striped">
                                        <tr>
                                            <th width="30%">Plot No</th>
                                            <td>{{ $property->plot_no ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Makani Number</th>
                                            <td>{{ $property->makani_number ?? '-' }}</td>
                                        </tr>


                                        <tr>
                                            <th>Area</th>
                                            <td>{{ $property->area->area_name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Locality</th>
                                            <td>{{ $property->locality->locality_name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Latitude</th>
                                            <td>{{ $property->latitude ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Longitude</th>
                                            <td>{{ $property->longitude ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Address</th>
                                            <td>{{ $property->address ?? '-' }}</td>
                                        </tr>

                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- RIGHT SIDE --}}
                        <div class="col-lg-4">

                            <h4 class="text-primary">
                                <i class="fas fa-info-circle"></i> Meta Details
                            </h4>

                            <div class="text-muted">
                                {{-- <p class="text-sm">
                                    Company
                                    <b class="d-block">{{ $property->company->name ?? '-' }}</b>
                                </p> --}}

                                <p class="text-sm">
                                    Added By
                                    <b
                                        class="d-block">{{ $property->addedBy->first_name . '-' . $property->addedBy->last_name }}</b>
                                </p>

                                <p class="text-sm">
                                    Updated By
                                    <b
                                        class="d-block">{{ $property->addedBy->first_name . '-' . $property->addedBy->last_name }}</b>
                                </p>

                                <p class="text-sm">
                                    Created On
                                    <b class="d-block">{{ \Carbon\Carbon::parse($property->created_at)->format('d M Y') }}
                                    </b>
                                </p>
                                <p class="text-sm">
                                    Updated On
                                    <b class="d-block">{{ \Carbon\Carbon::parse($property->updated_at)->format('d M Y') }}
                                    </b>
                                </p>

                                <p class="text-sm">
                                    Remarks
                                    <b class="d-block">{{ $property->remarks }}</b>
                                </p>
                                <p class="text-sm">
                                    Location
                                    <a href="{{ $property->location }}" class="d-block text-blue"
                                        target="_blank">{{ $property->location }}</a>
                                </p>
                            </div>

                            @if ($property->latitude && $property->longitude)
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-map-marker-alt"></i> Property Location
                                        </h3>
                                    </div>
                                    <div class="card-body p-0">
                                        <div id="propertyMap" style="height: 250px; width: 100%;"></div>
                                    </div>
                                </div>
                            @endif




                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>
@endsection
@section('custom_js')
    {{-- <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script> --}}
    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            const lat = {{ $property->latitude ?? 0 }};
            const lng = {{ $property->longitude ?? 0 }};

            if (!lat || !lng) return;

            // Initialize map
            const map = L.map('propertyMap').setView([lat, lng], 15);

            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            // Add a marker
            L.marker([lat, lng]).addTo(map)
                .bindPopup("{{ $property->property_name }}")
                .openPopup();
        });
    </script> --}}
    <script>
        function initMap() {
            const lat = {{ $property->latitude ?? 0 }};
            const lng = {{ $property->longitude ?? 0 }};

            if (!lat || !lng) return;

            const location = {
                lat: lat,
                lng: lng
            };

            const map = new google.maps.Map(document.getElementById("propertyMap"), {
                zoom: 15,
                center: location,
            });

            const marker = new google.maps.Marker({
                position: location,
                map: map,
                title: "{{ $property->property_name }}"
            });

            const infoWindow = new google.maps.InfoWindow({
                content: "{{ $property->property_name }}"
            });

            marker.addListener("click", () => {
                infoWindow.open(map, marker);
            });
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD46-CF9pTGIQpnKNkvc1eeZwBH2pQ70qQ&callback=initMap" async
    @endsection
