@extends('admin.layout.admin_master')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Contract details</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                            <li class="breadcrumb-item active">Contract details</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <!-- Main content -->
                        <div class="invoice p-3 mb-3">
                            {{-- <div class="text-uppercase text-bold text-info">
                                {{ $contract->contract_type->contract_type }} Project
                            </div> --}}

                            <span class="price-badge badge badge-danger">
                                {{ $contract->contract_type->contract_type }} Project
                            </span>
                            <!-- title row -->

                            <!-- info row -->
                            <div class="row invoice-info p-2">
                                <div class="col-sm-6">
                                    <h5 class="fw-bold text-primary mb-2">Vendor Details</h5>
                                    <address>
                                        <span>{{ 'P - ' . $contract->project_number }}</span></br>
                                        {{-- <span>{{ strtoupper($contract->vendor->vendor_name) }}</span></br> --}}
                                        <a href="{{ route('vendors.show', $contract->vendor_id) }}" class="linkhover"
                                            target="_blank">
                                            {{ strtoupper($contract->vendor->vendor_name) }}
                                        </a><br>
                                        <span>{{ strtoupper($contract->company->company_name) }}</span></br>
                                        <span>{!! strtoupper($contract->contact_person) . ' - ' . $contract->contact_number ?? ' - ' !!}</span><br>
                                        {{-- <span>{{ strtoupper($contract->property->property_name) }}</span></br> --}}
                                        <a href="{{ route('property.show', $contract->property->id) }}" class="linkhover"
                                            target="_blank">
                                            {{ strtoupper($contract->property->property_name) }}
                                        </a><br>
                                        <span>{{ strtoupper($contract->area->area_name) }}</span>,
                                        <span>{{ strtoupper($contract->locality->locality_name) }}</span></br>
                                        <span>{{ $contract->contract_detail->start_date }}</span>
                                        -
                                        <span>{{ $contract->contract_detail->end_date }}</span><br>

                                        <span>{{ strtoupper($contract->contract_unit->unit_type_count) }}</span>
                                        </br>
                                    </address>
                                </div>
                                <!-- /.col -->
                                <div class="col-sm-6 float-xl-right">
                                    <span class="float-xl-right ">
                                        <h5 class="fw-bold text-success mb-2">Financial Overview</h5>
                                        <address>
                                            <strong>Total Payment to Vendor</strong> -
                                            {{ $contract->contract_rentals->total_payment_to_vendor }} <br>

                                            <strong>Total OTC</strong> -
                                            {{ $contract->contract_rentals->total_otc }} <br>

                                            <strong>Profit</strong> -
                                            {!! $contract->contract_rentals->profit_percentage . '%' !!} <br>

                                            <strong>Expected Profit</strong> -
                                            {{ $contract->contract_rentals->expected_profit }} <br>

                                            <strong>ROI</strong> -
                                            {!! $contract->contract_rentals->roi_perc . '%' !!} <br>

                                            <strong>Total Rental</strong> -
                                            {{ $contract->contract_rentals->rent_receivable_per_annum }} <br>

                                            <strong>Total Contract Amount</strong> -
                                            {{ $contract->contract_rentals->rent_per_annum_payable }} <br>
                                        </address>

                                    </span>
                                </div>
                            </div>
                            <!-- /.row -->

                            <!-- Table row -->
                            <div class="row card">
                                <div class="card-header text-center py-3 shadow-sm rounded-top">
                                    <h4 class="mb-0 text-uppercase">Payment Details</h4>
                                </div>
                                <div class="col-12 table-responsive card-body">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Payment Mode</th>
                                                <th>Bank / Cheque Details </th>
                                                <th>Amount</th>
                                                <th>Beneficiary</th>
                                                <th>Paid On</th>
                                                <th>Composition</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($contract->contract_payments->contractPaymentDetails as $details)
                                                @php
                                                    $bgColor = '';
                                                    if ($details->paid_status == 0) {
                                                        $bgColor = '#ffcccc';
                                                    } elseif ($details->paid_status == 1) {
                                                        $bgColor = '#dbffdb';
                                                    } elseif ($details->paid_status == 2) {
                                                        $bgColor = '#c5f5ff';
                                                    }
                                                @endphp
                                                <tr style="background-color: {{ $bgColor }}">
                                                    <td>{{ $details->payment_date }}</td>
                                                    <td> {{ strtoupper($details->payment_mode->payment_mode_name) }}</td>
                                                    <td>
                                                        @if ($details->payment_mode->payment_mode_name == 'Cheque')
                                                            {!! 'Cheque no -' . $details->cheque_no . ' , ' . strtoupper($details->bank->bank_name) !!}
                                                        @elseif($details->payment_mode->payment_mode_name == 'Bank Transfer')
                                                            {{ strtoupper($details->bank->bank_name) }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>{{ $details->payment_amount }}</td>
                                                    <td>{{ strtoupper($contract->contract_payments->beneficiary) }}</td>
                                                    <td>{{ $details->paid_date ?? ' - ' }}</td>
                                                    <td>
                                                        {{ 'RENT ' . $loop->iteration . '/' . $contract->contract_payments->installment->installment_name }}
                                                    </td>


                                                </tr>
                                            @endforeach


                                        </tbody>
                                    </table>


                                    @php
                                        $total_paid = 0;
                                        $total_to_pay = 0;

                                        foreach ($contract->contract_payments->contractPaymentDetails as $details) {
                                            // Clean the values to ensure they are numeric
                                            // $payment_amount = (float) str_replace(',', '', $details->payment_amount ?? 0);
                                            // $paid_amount = (float) str_replace(',', '', $details->paid_amount ?? 0);
                                            $payment_amount = toNumeric($details->payment_amount);
                                            $paid_amount = toNumeric($details->paid_amount);

                                            $total_to_pay += $payment_amount;
                                            $total_paid += $paid_amount;

                                            // $total_to_pay += (float) ($details->payment_amount ?? 0);
                                            // $total_paid += (float) ($details->paid_amount ?? 0);
                                        }

                                        $remaining_amount = $total_to_pay - $total_paid;
                                    @endphp


                                    {{-- <div class="row"> --}}
                                    {{-- <div class="col-6"> --}}
                                    {{-- <p class="lead text-danger"><strong>Amount Due 2/22/2014</strong></p> --}}
                                    <div class="float-xl-right mt-1">
                                        <span> <strong>Total Paid :
                                            </strong>{{ number_format($total_paid) }}</span><br>
                                        <span> <strong>Remaining :
                                            </strong>{{ number_format($remaining_amount) }}</span>
                                    </div>
                                    {{-- </div> --}}
                                    <!-- /.col -->
                                    {{-- </div> --}}
                                    <!-- /.row -->

                                </div>
                                <!-- /.col -->
                            </div>
                            <!-- /.row -->

                            <!-- Table row -->
                            <div class="row card">
                                <div class="card-header text-center py-3 shadow-sm rounded-top">
                                    <h4 class="mb-0 text-uppercase">Property Unit Details</h4>
                                </div>
                                <div class="col-12 table-responsive card-body">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Unit Number</th>
                                                <th>Unit Type</th>
                                                <th>Property type</th>
                                                <th>Floor Number</th>
                                                <th>Unit Status</th>
                                                <th>Unit Rent Per Annum</th>
                                                <th>Partition / Bedspace / Room</th>
                                                <th>No of partition / Bedspace / Room</th>
                                                <th>Rent per partition / Bedspace / Room</th>
                                                <th>Rent per Flat</th>
                                                <th>Unit Profit %</th>
                                                <th>Unit Profit</th>
                                                <th>Unit Revenue</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($contract->contract_unit_details as $unitDetails)
                                                <tr>
                                                    <td>{{ strtoupper($unitDetails->unit_number) }}</td>
                                                    <td>{{ strtoupper($unitDetails->unit_type->unit_type) }}</td>
                                                    <td>{{ strtoupper($unitDetails->property_type->property_type) }}
                                                    <td>{{ strtoupper($unitDetails->floor_no) }}</td>
                                                    <td>{{ strtoupper($unitDetails->unit_status->unit_status) }}</td>
                                                    <td>{{ 'AED ' . $unitDetails->unit_rent_per_annum }}</td>
                                                    </td>
                                                    <td>
                                                        {{ subunittypeName($unitDetails->subunittype) }}
                                                        {{-- @if ($unitDetails->partition)
                                                            PARTITION
                                                        @elseif($unitDetails->bedspace)
                                                            BEDSPACE
                                                        @elseif($unitDetails->room)
                                                            ROOM
                                                        @else
                                                            -
                                                        @endif --}}

                                                    </td>
                                                    <td>
                                                        {{ subunittypeCount($unitDetails)['subunitCount'] }}
                                                        {{-- @if ($unitDetails->partition)
                                                            {{ $unitDetails->total_partition }}
                                                        @elseif($unitDetails->bedspace)
                                                            {{ $unitDetails->total_bedspace }}
                                                        @elseif($unitDetails->room)
                                                            {{ $unitDetails->total_room }}
                                                        @else
                                                            -
                                                        @endif --}}
                                                    </td>
                                                    <td>
                                                        {{ subunittypeCount($unitDetails)['unitrent'] }}
                                                        {{-- @if ($unitDetails->partition)
                                                            {{ $unitDetails->rent_per_partition }}
                                                        @elseif($unitDetails->bedspace)
                                                            {{ $unitDetails->rent_per_bedspace }}
                                                        @elseif($unitDetails->room)
                                                            {{ $unitDetails->rent_per_room }}
                                                        @else
                                                            -
                                                        @endif --}}
                                                    </td>
                                                    <td>{{ $unitDetails->rent_per_flat ?? ' - ' }}</td>
                                                    <td>{{ $unitDetails->unit_profit_perc ?? ' - ' }}</td>
                                                    <td>{{ $unitDetails->unit_profit ?? ' - ' }}</td>
                                                    <td>{{ 'AED ' . $unitDetails->unit_revenue ?? ' - ' }}</td>
                                                </tr>
                                            @endforeach


                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.col -->
                            </div>
                            <!-- /.row -->
                            {{-- {{ DD($contract->contract_payments->contractPaymentDetails) }} --}}


                            @if ($allChildren->count() > 0)
                                <div class="row card">
                                    <div class="card-header text-center py-3 shadow-sm rounded-top">
                                        <h4 class="mb-0 text-uppercase">Contract Renewal Details</h4>
                                    </div>
                                    {{-- <div class="col-12 table-responsive card-body">
                                        <div class="d-flex justify-content-center row">
                                            @foreach ($contract->children as $renewal)
                                                <div class="col-3">
                                                    <div class="bg-gradient-olive bg-info info-box">
                                                        <span class="info-box-icon"><i
                                                                class="fas fa-file-contract"></i></span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text">Project Number</span>
                                                            <span class="info-box-number">P -
                                                                {{ $renewal->project_number }}</span>
                                                        </div>
                                                        <!-- /.info-box-content -->
                                                    </div>
                                                </div>
                                            @endforeach

                                        </div>
                                    </div> --}}
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Project Number</th>
                                                <th>Renewal Count</th>
                                                <th>Total Rent Payable</th>
                                                <th>Profit %</th>
                                                <th>Deposit</th>
                                                <th>Commission</th>
                                                <th>Profit Earned</th>
                                                <th>Installments Received</th>
                                                <th>Rental Received</th>
                                                <th>Old Monthly Rental</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($allChildren as $renewal)
                                                @php
                                                    if ($renewal->renewal_count > 0) {
                                                        $renewal_count = 'Renewal - ' . $renewal->renewal_count;
                                                    } else {
                                                        $renewal_count = 'New';
                                                    }
                                                @endphp
                                                <tr>
                                                    <td><a href="{{ route('contract.show', $renewal->id) }}"
                                                            style="text-decoration: none; color: #17a2b8;">P -
                                                            {{ $renewal->project_number }}</a></td>
                                                    <td>{{ $renewal_count }}</td>
                                                    <td>{{ $renewal->contract_rentals->rent_per_annum_payable }}</td>
                                                    <td>{{ $renewal->contract_rentals->profit_percentage }}</td>
                                                    <td>{{ $renewal->contract_rentals->deposit }}</td>
                                                    <td>{{ $renewal->contract_rentals->commission }}</td>
                                                    <td>{{ $renewal->contract_rentals->expected_profit }}</td>
                                                    <td>{{ $renewal->contract_rentals->installment->installment_name }}
                                                    </td>
                                                    <td>{{ $renewal->contract_rentals->rent_receivable_per_annum }}</td>
                                                    <td>{{ $renewal->contract_rentals->rent_receivable_per_month }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                </div>
                            @endif


                            <!-- this row will not appear when printing -->
                            <div class="row no-print">
                                <div class="col-12 d-xl-flex justify-content-between">
                                    {{-- <a href="{{ route('contract.index') }}" class="btn btn-default">Back</a> --}}
                                    <a href="{{ route('contract.index') }}" class="btn btn-info"><i
                                            class="fas mr-2 fa-arrow-left"></i>Back</a>

                                    <div class="mt-2 mt-xl-0">


                                        @if ($contract->contract_status != 3)
                                            @if (Gate::allows('contract.edit') && $contract->has_agreement == 0)
                                                <a class="btn btn-secondary"
                                                    href="{{ route('contract.edit', $contract->id) }}">Edit</a>
                                            @endif


                                            @if ($contract->is_scope_generated == 0)
                                                <button class="btn btn-primary"
                                                    onclick="generateScope({{ $contract->id }})">
                                                    <i class="fas fa-envelope-open-text"></i> Generate Scope</button>
                                            @elseif ($contract->is_vendor_contract_uploaded == 0)
                                                {{-- <button type="button" class="btn btn-warning "><i class="fas fa-upload"></i>
                                                Upload Contract </button> --}}
                                                <button class="btn btn-primary"
                                                    onclick="generateScope({{ $contract->id }})">
                                                    <i class="fas fa-download"></i> Update Scope
                                                </button>
                                            @elseif($contract->contract_status == 2)
                                                <a href="{{ route('contract.documents', $contract->id) }}"
                                                    class="btn btn-warning" title="Upload Documents">
                                                    Documents
                                                </a>
                                                <button type="button" class="btn btn-info mt-1 mt-xl-0">
                                                    <i class="fas fa-envelope-open-text"></i> Generate Acknoledgement
                                                </button>
                                            @endif
                                        @endif

                                        @if ($contract->contract_status != 0)
                                            @if (Gate::allows('contract.document_upload'))
                                                <a href="{{ route('contract.documents', $contract->id) }}"
                                                    class="btn btn-warning" title="Upload Documents"> <i
                                                        class="fas fa-upload"></i>
                                                    Document
                                                </a>
                                            @endif
                                        @endif

                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.Contract details -->
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection
@section('custom_js')
    <script>
        function generateScope(id) {
            showLoader();
            $.ajax({
                type: "GET",
                url: "{{ url('export-building-summary') }}/" + id, // ← correct
                dataType: "json",
                success: function(response) {

                    // console.log(response);
                    // Create a temporary link
                    var link = document.createElement('a');
                    link.href = response.file_url;
                    link.download = ''; // Let browser pick filename from response
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    hideLoader();
                    swal.fire({
                        icon: 'success',
                        title: 'Scope Generated',
                        text: 'Please upload Vendor Contract and Proceed with Contract Approval.',
                        type: 'success'
                    }).then((result) => {
                        // This code runs after the "OK" button is clicked
                        window.location.href = response
                            .redirect_url; // Replace 'next_page.html' with your URL
                    });


                    // // 2. REDIRECT AFTER 1 SEC
                    // setTimeout(() => {

                    // window.location.reload();

                    // }, 800);

                    // if (response.download_url) {
                    //     // STEP 2: IMPORTANT → browser redirect (NOT AJAX)
                    //     window.location.href = response.download_url;
                    // } else {
                    //     alert("Download link not received.");
                    // }
                },
                error: function(xhr) {
                    hideLoader();
                    alert("Failed to export summary!");
                }
            });
        }
    </script>
@endsection
