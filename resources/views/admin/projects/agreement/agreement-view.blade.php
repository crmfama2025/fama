@extends('admin.layout.admin_master')
@section('custom_css')
@endsection
@section('content')
    @php
        $business_type = ' - ';
        $type = $agreement->contract->contract_unit->business_type;
        $contract_type = $agreement->contract->contract_type_id;
        if ($type == 1) {
            $business_type = 'B2B';
        } else {
            $business_type = 'B2C';
        }

    @endphp
    {{-- {{ dd($agreement) }} --}}
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Installment details</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Installment details</li>
                        </ol>
                        <!-- Back to List Button -->
                        <a href="{{ route('agreement.index') }}" class="btn btn-info float-sm-right mx-2 btn-sm ml-2">
                            <i class="fa fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <section class="content">
            {{-- {{ dd($agreement->contract->contract_type_id) }} --}}
            <div class="container-fluid">

                <div class="row">
                    <div class="col-12">


                        <!-- Main content -->
                        <div class="invoice p-3 mb-3">
                            <span
                                class="{{ 'badge badge-danger ' . ($agreement->contract->contract_type_id == 1 ? 'price-badge-df ' : 'price-badge-ff') }}">
                                {{ $agreement->contract->contract_type->contract_type }} Project
                            </span>

                            <!-- title row -->

                            <!-- info row -->
                            <div class="row invoice-info p-2">
                                <div class="col-sm-6">
                                    <h5 class="font-weight-bold text-primary mb-2">Vendor Details</h5>
                                    <address>
                                        <span class="project_id">P - {{ $agreement->contract->project_number }}</span></br>
                                        <span
                                            class="vendor_name">{{ strtoupper($agreement->contract->vendor->vendor_name) }}</span></br>
                                        <span
                                            class="name">{{ strtoupper($agreement->contract->company->company_name) }}</span></br>
                                        <span
                                            class="mobile">{{ strtoupper($agreement->contract->vendor->vendor_phone) }}</span></br>
                                        <span class="email">{{ $agreement->contract->vendor->vendor_email }}</span></br>
                                        <span class="area">{{ strtoupper($agreement->contract->area->area_name) }}</span>,
                                        <span
                                            class="locality">{{ strtoupper($agreement->contract->locality->locality_name) }}</span>,
                                        <span
                                            class="building">{{ strtoupper($agreement->contract->property->property_name) }}
                                            -
                                            @foreach ($agreement->agreement_units as $unit)
                                                {{ strtoupper($unit->contractUnitDetail->unit_number) }}@if (!$loop->last)
                                                    ,
                                                @endif
                                            @endforeach
                                        </span></br>

                                    </address>
                                </div>
                                <!-- /.col -->
                                <div class="col-sm-6 float-xl-right">

                                    <span class="float-xl-right">

                                        <h5 class="font-weight-bold text-success mb-2">Tenant Details -
                                            <span class="text-danger">{{ $business_type }}</span>
                                        </h5>
                                        <address>
                                            <span
                                                class="vendor_name">{{ strtoupper($agreement->tenant->tenant_name) }}</span></br>
                                            <span class="mobile">{{ $agreement->tenant->tenant_mobile }}</span></br>
                                            <span class="email">{{ $agreement->tenant->tenant_email }}</span></br>
                                            <span
                                                class="area">{{ strtoupper($agreement->contract->area->area_name) }}</span>,
                                            <span
                                                class="locality">{{ strtoupper($agreement->contract->locality->locality_name) }}</span>,
                                            <span class="building">
                                                {{ strtoupper($agreement->contract->property->property_name) }}
                                                -
                                                @foreach ($agreement->agreement_units as $unit)
                                                    {{ strtoupper($unit->contractUnitDetail->unit_number) }}@if (!$loop->last)
                                                        ,
                                                    @endif
                                                @endforeach
                                            </span></br>
                                            <span
                                                class="start_date">{{ \Carbon\Carbon::parse($agreement->start_date)->format('d/m/Y') }}</span>
                                            - <span
                                                class="end_date">{{ \Carbon\Carbon::parse($agreement->end_date)->format('d/m/Y') }}</span></br>
                                            {{-- <span class="unit_type">
                                                @foreach ($agreement->agreement_units as $unit)
                                                    {{ strtoupper($unit->contractUnitDetail->unit_type->unit_type) }}
                                                    @if (!$loop->last)
                                                        ,
                                                    @endif
                                                @endforeach
                                            </span>
                                            - <span class="inst_mode">12 /
                                                Bank</span></br> --}}
                                            @if ($agreement->agreement_status == 1)
                                                <span class="badge badge-warning px-3 py-2">Terminated</span>
                                                <span class="btn btn-success btn-sm px-3 py-1 ml-2" data-toggle="modal"
                                                    data-target="#terminationReasonModal"
                                                    data-reason="{{ $agreement->terminated_reason }}"
                                                    data-date="{{ $agreement->terminated_date }}">
                                                    <i class="fas fa-info-circle mr-1"></i> Reason
                                                </span>
                                            @elseif($agreement->agreement_status == 2)
                                                <span class="badge badge-danger px-3 py-2">Expired</span>
                                            @else
                                                <span class="badge badge-success px-3 py-2">Active</span>
                                            @endif
                                        </address>
                                    </span>
                                </div>
                            </div>
                            <!-- /.row -->

                            <div class="row d-flex justify-content-center">
                                <div class="col-md-6">
                                    <div class="card card-widget  shadow-sm">
                                        <div class="bg-gradient-olive pl-4 py-2 widget-user-header">

                                            <h5 class="widget-user-username"><i
                                                    class="fa fa-solid mx-1 fa-user"></i>{{ $agreement->tenant->tenant_name }}
                                                </h3>
                                                <h6 class="widget-user-desc"><i
                                                        class="fa  fa-solid fa-inbox mx-1"></i>{{ $agreement->tenant->tenant_email }}
                                            </h5>
                                        </div>
                                        <div class="card-footer p-0">
                                            <ul class="nav flex-column">
                                                <li class="nav-item">
                                                <li class="nav-item">
                                                    <div class="nav-link">
                                                        Phone Number
                                                        <span
                                                            class="float-right text-bold">{{ $agreement->tenant->tenant_mobile }}</span>
                                                    </div>
                                                </li>
                                                </li>
                                                <li class="nav-item">
                                                    <div class="nav-link">
                                                        Contact person <span
                                                            class="float-right text-bold text-capitalize ">{{ $agreement->tenant->contact_person }}</span>
                                                    </div>
                                                </li>
                                                <li class="nav-item">
                                                    <div class="nav-link">
                                                        Contact Number <span
                                                            class="float-right text-bold">{{ $agreement->tenant->contact_number }}</span>
                                                    </div>
                                                </li>
                                                <li class="nav-item">
                                                    <div class="nav-link">
                                                        Contact Email <span
                                                            class="float-right text-bold">{{ $agreement->tenant->contact_email }}</span>
                                                    </div>
                                                </li>

                                            </ul>
                                        </div>
                                    </div>
                                    <!-- /.widget-user -->
                                </div>
                                {{-- <div class="card table-responsive p-0 col-md-6 mt-sm-3 mt-lg-0">
                                    <h5 class="border-bottom border-top p-2 text-center text-maroon">Unit Details</h5>
                                    <div class="bg-gradient-lightblue card-header py-2 text-center text-white">
                                        <h5 class="mb-0">Unit Details</h5>
                                    </div>
                                    <table class="bg-gradient-lightblue table table-hover text-nowrap text-white">
                                        <thead>
                                            <tr>
                                                <th>Unit Number</th>
                                                <th>Unit Type</th>
                                                @if ($type == 2)
                                                    <th>Subunit Number</th>
                                                @elseif ($type == 1)
                                                    <th>Total subunits</th>
                                                @endif
                                                <th>Subunit Type</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($agreement->agreement_units as $unit)
                                                <tr>
                                                    <td>{{ $unit->contractUnitDetail->unit_number }}</td>
                                                    <td>{{ $unit->contractUnitDetail->unit_type->unit_type }}</td>
                                                    @if ($type == 2)
                                                        <td>{{ $unit->contractSubunitDetail->subunit_no ?? ' ' }}</td>
                                                    @elseif ($type == 1)
                                                        <th>{{ $unit->contractUnitDetail->subunitcount_per_unit ?? ' ' }}
                                                        </th>
                                                    @endif

                                                    @if ($unit->contractUnitDetail->subunittype == 1)
                                                        <td>Partition</td>
                                                    @elseif ($unit->contractUnitDetail->subunittype == 2)
                                                        <td>BedSpace</td>
                                                    @elseif ($unit->contractUnitDetail->subunittype == 3)
                                                        <td>Room</td>
                                                    @elseif ($unit->contractUnitDetail->subunittype == 4)
                                                        <td>Full Flat</td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div> --}}
                                <div class="col-md-6 mt-sm-3 mt-lg-0">
                                    <div class="card shadow-sm border-0 rounded">
                                        <div class="card-header text-white text-center py-2 bg-maroon"
                                            style=" font-size:18px; font-weight:600;">
                                            Unit Details
                                        </div>

                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover mb-0 ">
                                                <thead style=" color:white;">
                                                    <tr class="text-center bg-cyan">
                                                        <th>Unit Number</th>
                                                        <th>Unit Type</th>

                                                        @if ($type == 2)
                                                            <th>Subunit Number</th>
                                                        @elseif ($type == 1)
                                                            <th>Total Subunits</th>
                                                        @endif

                                                        <th>Subunit Type</th>
                                                        @if ($type == 1 && $contract_type == 1)
                                                            <th>Rent per Month</th>
                                                            <th>Split Rent</th>
                                                        @endif
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    @foreach ($agreement->agreement_units as $unit)
                                                        {{-- {{ dd($unit) }} --}}
                                                        <tr class="text-center">
                                                            <td>{{ $unit->contractUnitDetail->unit_number }}</td>
                                                            <td>{{ $unit->contractUnitDetail->unit_type->unit_type }}</td>

                                                            @if ($type == 2)
                                                                <td>{{ $unit->contractSubunitDetail->subunit_no ?? '-' }}
                                                                </td>
                                                            @elseif ($type == 1)
                                                                <td>{{ $unit->contractUnitDetail->subunitcount_per_unit ?? '-' }}
                                                                </td>
                                                            @endif

                                                            <td>
                                                                @php
                                                                    $types = [
                                                                        1 => 'Partition',
                                                                        2 => 'BedSpace',
                                                                        3 => 'Room',
                                                                        4 => 'Full Flat',
                                                                    ];
                                                                @endphp
                                                                {{ $types[$unit->contractUnitDetail->subunittype] ?? '-' }}
                                                            </td>
                                                            @if ($type == 1 && $contract_type == 1)
                                                                <td>{{ $unit->rent_per_month ?? '-' }}</td>
                                                                @php
                                                                    $hasBifurcation = $unit->agreementSubunitRentBifurcation->isNotEmpty();
                                                                @endphp

                                                                <td>
                                                                    <button type="button"
                                                                        class="btn btn-sm {{ $hasBifurcation ? 'btn-warning' : 'btn-primary' }} ms-2 openRentModal"
                                                                        data-unit-id="{{ $unit->id }}"
                                                                        data-subunit-count="{{ $unit->contractUnitDetail->subunitcount_per_unit }}"
                                                                        data-subunits='@json($unit->contractUnitDetail->contractSubUnitDetails)'
                                                                        data-units='@json($unit->contractUnitDetail)'
                                                                        data-agreement-id="{{ $agreement->id }}"
                                                                        title="{{ $hasBifurcation ? 'Edit Rent Bifurcation' : 'Split Rent' }}">

                                                                        <i
                                                                            class="fa {{ $hasBifurcation ? 'fa-edit' : 'fa-sitemap' }}"></i>
                                                                    </button>
                                                                </td>
                                                            @endif
                                                        </tr>
                                                    @endforeach
                                                </tbody>

                                            </table>
                                        </div>
                                    </div>
                                </div>



                            </div>




                            <div id="unitAccordion" class="mt-sm-3">
                                @foreach ($agreement->agreement_units as $unit)
                                    @php
                                        $isFirst = $loop->first;
                                    @endphp
                                    <div class="card mb-2">
                                        <!-- Accordion Header -->
                                        <div class="card-header agreement-accordion" id="heading{{ $unit->id }}">
                                            <h5 class="mb-0">
                                                <button
                                                    class="btn btn-link text-uppercase font-weight-bold text-blue collapsed d-flex justify-content-between w-100"
                                                    type="button" data-toggle="collapse"
                                                    data-target="#collapse{{ $unit->id }}" aria-expanded="false"
                                                    aria-controls="collapse{{ $unit->id }}">
                                                    <span style="letter-spacing: .5px;">
                                                        UNIT: {{ $unit->contractUnitDetail->unit_number }}
                                                        <span class="ml-3 text-green font-weight-bolder"
                                                            style="letter-spacing: .5px;">
                                                            Type:
                                                            {{ $unit->contractUnitDetail->unit_type->unit_type ?? '-' }}
                                                            @if ($unit->contractSubunitDetail)
                                                                @if ($unit->contractSubunitDetail->subunit_type == 1)
                                                                    | Partition:
                                                                @elseif ($unit->contractSubunitDetail->subunit_type == 2)
                                                                    | Bedspace:
                                                                @elseif ($unit->contractSubunitDetail->subunit_type == 3)
                                                                    | Room:
                                                                @else
                                                                    | Full Flat:
                                                                @endif
                                                                {{ $unit->contractSubunitDetail->subunit_no }}
                                                            @endif
                                                            | Rent/Month: {{ number_format($unit->rent_per_month, 2) }}
                                                            | Rent/Annum:
                                                            {{ number_format($unit->unit_revenue, 2) }}
                                                        </span>
                                                    </span>
                                                    <span class="arrow">&#9654;</span>
                                                </button>

                                            </h5>
                                        </div>

                                        <!-- Accordion Body -->
                                        <div id="collapse{{ $unit->id }}"
                                            class="collapse {{ $isFirst ? 'show' : '' }}"
                                            aria-labelledby="heading{{ $unit->id }}" data-parent="#unitAccordion">
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table mb-1">
                                                        <thead>
                                                            <tr>
                                                                <th>Date</th>
                                                                <th>Payment Mode</th>
                                                                <th>Amount</th>
                                                                <th>Favouring</th>
                                                                <th>Paid On</th>
                                                                <th>Paid Amount</th>
                                                                <th>Balance Amount</th>
                                                                <th>Status of Termination</th>
                                                                <th>Composition</th>
                                                                <th>Invoice Upload</th>
                                                                <th>View Invoice</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>

                                                            @foreach ($agreement->agreement_payment->agreementPaymentDetails->where('agreement_unit_id', $unit->id) as $detail)
                                                                @php
                                                                    $bgColor = match ($detail->is_payment_received) {
                                                                        0 => '#ffcccc',
                                                                        1 => '#dbffdb',
                                                                        2 => '#c5f5ff',
                                                                        3 => '#fff5c5',
                                                                        default => '',
                                                                    };
                                                                @endphp
                                                                @php
                                                                    $totalPaid = 0;
                                                                    $totalBalance = 0;
                                                                    $totalBalance = 0;
                                                                    $paid_on = null;
                                                                @endphp

                                                                {{-- @dump($detail->receivedPayments) --}}

                                                                @foreach ($detail->receivedPayments ?? [] as $receivable)
                                                                    @php
                                                                        $totalPaid +=
                                                                            (float) ($receivable->paid_amount ?? 0);
                                                                        $totalBalance =
                                                                            $receivable->pending_amount ?? 0;
                                                                        if (
                                                                            $totalPaid > 0 &&
                                                                            !empty($receivable->paid_date)
                                                                        ) {
                                                                            $paid_on = $receivable->paid_date;
                                                                        }
                                                                    @endphp
                                                                @endforeach
                                                                <tr>
                                                                    <td
                                                                        style="background-color: {{ $bgColor }} !important;">
                                                                        {{ \Carbon\Carbon::parse($detail->payment_date)->format('d/m/Y') }}
                                                                    </td>
                                                                    <td
                                                                        style="background-color: {{ $bgColor }} !important;">
                                                                        {{ $detail->paymentMode->payment_mode_name }}
                                                                        @if (!empty($detail->bank_id))
                                                                            - {{ ucfirst($detail->bank->bank_name) }}
                                                                        @endif
                                                                        @if (!empty($detail->cheque_number))
                                                                            - {{ ucfirst($detail->cheque_number) }}
                                                                        @endif
                                                                    </td>
                                                                    <td
                                                                        style="background-color: {{ $bgColor }} !important;">
                                                                        {{ number_format($detail->payment_amount, 2) }}
                                                                    </td>
                                                                    <td
                                                                        style="background-color: {{ $bgColor }} !important;">
                                                                        {{ $agreement->agreement_payment->beneficiary }}
                                                                    </td>
                                                                    <td
                                                                        style="background-color: {{ $bgColor }} !important;">
                                                                        {{-- {{ $detail->paid_date ?? '-' }} --}}
                                                                        {{ $paid_on ? \Carbon\Carbon::parse($paid_on)->format('d/m/Y') : '-' }}
                                                                    </td>
                                                                    {{-- @dump($totalPaid) --}}
                                                                    <td
                                                                        style="background-color: {{ $bgColor }} !important;">
                                                                        {{-- {{ number_format($detail->paid_amount, 2) ?? '-' }} --}}
                                                                        {{ number_format($totalPaid, 2) ?? '-' }}

                                                                    </td>
                                                                    <td
                                                                        style="background-color: {{ $bgColor }} !important;">
                                                                        {{-- {{ number_format($detail->paid_amount, 2) ?? '-' }} --}}
                                                                        {{ number_format($totalBalance, 2) ?? '-' }}

                                                                    </td>
                                                                    <td
                                                                        style="background-color: {{ $bgColor }} !important;">
                                                                        @if ($detail->terminate_status == 0)
                                                                            <span class="badge bg-success">Active</span>
                                                                        @else
                                                                            <span
                                                                                class="badge bg-warning text-dark">Terminated</span>
                                                                        @endif
                                                                    </td>

                                                                    <td
                                                                        style="background-color: {{ $bgColor }} !important;">
                                                                        RENT
                                                                        {{ $loop->iteration }}/{{ $agreement->agreement_payment->installment->installment_name }}
                                                                    </td>
                                                                    @can('agreement.invoice_upload')
                                                                        <td>
                                                                            <button type="button"
                                                                                class="btn btn-success btn-sm open-invoice-modal"
                                                                                title="Upload Invoice"
                                                                                data-detailId="{{ $detail->id }}"
                                                                                data-agreementId="{{ $agreement->id }}"
                                                                                @if ($detail->invoice) data-invoiceid="{{ $detail->invoice->id }}" @endif
                                                                                {{ $detail->terminate_status != 0 ? 'disabled' : '' }}><i
                                                                                    class="fas fa-file-upload"></i></a>

                                                                        </td>
                                                                    @endcan
                                                                    <td>
                                                                        @if ($detail->invoice)
                                                                            @php
                                                                                $filePath = asset(
                                                                                    'storage/' .
                                                                                        $detail->invoice->invoice_path,
                                                                                );

                                                                            @endphp

                                                                            <a href="{{ $filePath }}" target="_blank"
                                                                                class="btn btn-primary btn-sm"
                                                                                title="View Invoice">
                                                                                <i class="fas fa-file-pdf"></i> View
                                                                            </a>
                                                                        @else
                                                                            -
                                                                        @endif
                                                                    </td>

                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>

                                                    @php
                                                        $total_paid = 0;
                                                        $total_to_pay = 0;
                                                        foreach (
                                                            $agreement->agreement_payment->agreementPaymentDetails->where(
                                                                'agreement_unit_id',
                                                                $unit->id,
                                                            )
                                                            as $detail
                                                        ) {
                                                            foreach ($detail->receivedPayments ?? [] as $receivable) {
                                                                $total_paid += toNumeric($receivable->paid_amount);
                                                            }
                                                            $total_to_pay += toNumeric($detail->payment_amount);
                                                            // $total_paid += toNumeric($detail->paid_amount);
                                                        }
                                                        $remaining_amount = $total_to_pay - $total_paid;
                                                    @endphp
                                                    <div class="float-right">
                                                        <span><strong>Total Unit Revenue:</strong>
                                                            {{ number_format($total_to_pay, 2) }}</span><br>
                                                        <span><strong>Total Received:</strong>
                                                            {{ number_format($total_paid, 2) }}</span><br>
                                                        <span><strong>Remaining:</strong>
                                                            {{ number_format($remaining_amount, 2) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>





                            <!-- /.row -->

                            <!-- this row will not appear when printing -->
                            <div class="row no-print mt-2">
                                <div class="col-12 d-xl-flex justify-content-between">
                                    <a href="{{ route('agreement.index') }}" class="btn btn-info"><i
                                            class="fas mr-2 fa-arrow-left"></i>Back</a>

                                </div>
                            </div>
                        </div>
                        <!-- /.Contract details -->
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->

            <div class="modal fade" id="installment-edit">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Import</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="" id="ContractImportForm" method="POST" enctype="multipart/form-data">
                            <div class="modal-body">
                                <div class="card-body">
                                    <div id="payment-step" class="content" role="tabpanel"
                                        aria-labelledby="payment-step-trigger">
                                        <div class="form-group row">
                                            <div class="col md-4">
                                                <label for="exampleInputEmail1">Payment Mode</label>
                                                <select class="form-control select2" name="payment_mode"
                                                    id="payment_mode">
                                                    <option value="">Select</option>
                                                    <option value="1">Cash</option>
                                                    <option value="bank">Bank Transfer</option>
                                                    <option value="chq">Cheque</option>
                                                    <option value="cc">Credit card</option>
                                                </select>
                                            </div>
                                            <div class="col md-4">
                                                <label for="exampleInputEmail1">No. of Instalments</label>
                                                <select class="form-control select2" name="company_id" id="company_id">
                                                    <option value="">Select</option>
                                                    <option value="1">1</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="exampleInputEmail1">Interval</label>
                                                <input type="text" class="form-control" id="exampleInputEmail1"
                                                    placeholder="Interval" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <label for="exampleInputEmail1">First Payment Date</label>
                                                <div class="input-group date" id="firstpaymntdate"
                                                    data-target-input="nearest">
                                                    <input type="text" class="form-control datetimepicker-input"
                                                        data-target="#firstpaymntdate" placeholder="dd-mm-YYYY" />
                                                    <div class="input-group-append" data-target="#firstpaymntdate"
                                                        data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <label for="exampleInputEmail1">Payment Amount</label>
                                                <input type="text" class="form-control" id="exampleInputEmail1"
                                                    placeholder="Payment Amount">
                                            </div>


                                            <div class="col-md-4">
                                                <label for="exampleInputEmail1">Beneficiary</label>
                                                <input type="text" class="form-control" id="exampleInputEmail1"
                                                    placeholder="Beneficiary">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-4 bank">
                                                <label for="exampleInputEmail1">Bank Name</label>
                                                <select class="form-control select2" name="company_id" id="company_id">
                                                    <option value="">Select bank</option>
                                                    <option value="1">1</option>
                                                </select>
                                            </div>

                                            <div class="col-md-3 chq">
                                                <label for="exampleInputEmail1">Cheque No</label>
                                                <input type="text" class="form-control" id="exampleInputEmail1"
                                                    placeholder="Cheque No">
                                            </div>

                                            <div class="col-md-3 chq">
                                                <label for="exampleInputEmail1">Cheque Issuer</label>
                                                <select class="form-control select2" name="cheque_issuer"
                                                    id="cheque_issuer">
                                                    <option value="">Select</option>
                                                    <option value="self">Self</option>
                                                    <option value="other">Other</option>
                                                </select>
                                            </div>

                                            <div class="col-md-3 chqot">
                                                <label for="exampleInputEmail1">Cheque Issuer Name</label>
                                                <input type="text" class="form-control" id="exampleInputEmail1"
                                                    placeholder="Cheque Issuer Name">
                                            </div>

                                            <div class="col-md-3 chqot">
                                                <label for="exampleInputEmail1">Issuer ID</label>
                                                <input type="text" class="form-control" id="exampleInputEmail1"
                                                    placeholder="Issuer ID">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="button" id="submitBtn" class="btn btn-info">submit</button>
                            </div>
                        </form>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!-- /.modal -->

            <div class="modal fade" id="rentBifurcationModal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">

                        <form id="rentBifurcationForm" method="POST" action="#">
                            @csrf

                            <div class="modal-header">
                                <h5 class="modal-title">Rent Bifurcation</h5>
                                <button type="button" class="close" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">
                                <div class="py-2 float-lg-right">Total Unit Rent Per Month: <span
                                        class="text-danger font-weight-bold" id="totalUnitRent"></span></div>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Subunit</th>
                                            <th>Rent</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bifurcationRows"></tbody>
                                </table>
                                {{-- <p>Sum of Subunit Rents: <span id="sumSubunitRent">0.00</span></p> --}}
                                <p id="rentMismatchError" class="text-danger fw-bold" style="display:none;">
                                    Error: Sum of subunit rents does not match total unit rent!
                                </p>

                                <input type="hidden" name="contract_unit_details_id" id="bifurcationUnitId">
                                <input type="hidden" name="agreement_unit_id" id="agreementUnitId">
                                <input type="hidden" name="agreement_id" id="agreementId">
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" id="saveRentBifurcation">Save</button>
                            </div>

                        </form>

                    </div>
                </div>
            </div>






        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->


    <div class="modal fade" id="modal-invoiceUpload">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Upload Invoice</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" id="uploadForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="card-body">
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-form-label asterisk">Upload Invoice File</label>
                                <input type="file" name="invoice_path" id="" class="form-control"
                                    accept=".pdf,.jpg,.jpeg,.png" required>
                                <div class="invalid-feedback"></div>
                            </div>

                        </div>
                    </div>
                    <input type="hidden" name="agreement_id" id="agreement_id">
                    <input type="hidden" name="detail_id" id="detail_id">
                    <input type="hidden" name="invoice_id" id="invoice_id">

                    <!-- /.card-body -->
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-info terminate-btn uploadBtn">Save changes</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <div class="modal fade" id="terminationReasonModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content shadow-lg">

                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-ban mr-2"></i> Termination Details
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <div class="alert alert-light border-left border-danger p-3 shadow-sm">
                        <h6 class="font-weight-bold text-danger mb-2">
                            <i class="fas fa-info-circle mr-1"></i> Reason
                        </h6>
                        <p id="terminationReasonText" class="mb-0"></p>
                    </div>

                    <div id="terminationDateBox" class="mt-3 d-none">
                        <h6 class="font-weight-bold text-dark mb-1">
                            <i class="fas fa-calendar-alt mr-1 text-primary"></i> Termination Date
                        </h6>
                        <p id="terminationDateText" class="text-muted"></p>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">
                        Close
                    </button>
                </div>

            </div>
        </div>
    </div>
@endsection
@section('custom_js')
    <script>
        let agreement = @json($agreement);
        console.log(agreement);
    </script>
    <script>
        $(document).on('click', '.open-invoice-modal', function(e) {
            e.preventDefault();
            const agreementId = $(this).data('agreementid');
            const detailId = $(this).data('detailid');
            const invoiceId = $(this).data('invoiceid');
            $('#agreement_id').val(agreementId);
            $('#detail_id').val(detailId);
            $('#invoice_id').val(invoiceId);

            $('#modal-invoiceUpload').modal('show');
        });
    </script>
    <script>
        $('.uploadBtn').click(function(e) {
            e.preventDefault();
            const uploadBtn = $(this);
            var form = document.getElementById('uploadForm');
            var fdata = new FormData(form);

            // Clear previous errors
            $('#uploadForm .form-control').removeClass('is-invalid');
            $('#uploadForm .invalid-feedback').text('');

            // Front-end required validation for file
            const fileInput = $('#uploadForm [name="invoice_path"]');
            if (!fileInput.val()) {
                fileInput.addClass('is-invalid');
                fileInput.siblings('.invalid-feedback').text('Invoice file is required.');
                return;
            }

            uploadBtn.prop('disabled', true);

            $.ajax({
                url: "{{ url('agreement-invoice-upload') }}/",
                type: 'POST',
                data: fdata,
                processData: false,
                contentType: false,
                success: function(response) {
                    toastr.success(response.message);
                    $('#modal-invoiceUpload').modal('hide');
                    window.location.reload();
                },
                error: function(xhr) {
                    uploadBtn.prop('disabled', false);
                    const response = xhr.responseJSON;
                    if (xhr.status === 422 && response?.errors) {
                        $.each(response.errors, function(key, messages) {
                            const input = $('#uploadForm [name="' + key + '"]');
                            input.addClass('is-invalid');
                            input.siblings('.invalid-feedback').text(messages[0]);
                        });
                    } else if (response.message) {
                        toastr.error(response.message);
                    }
                }
            });
        });
        $('#terminationReasonModal').on('show.bs.modal', function(event) {
            let reason = $(event.relatedTarget).data('reason') || 'No reason provided';
            let date = $(event.relatedTarget).data('date') || null;

            $('#terminationReasonText').text(reason);

            if (date) {
                $('#terminationDateText').text(date);
                $('#terminationDateBox').removeClass('d-none');
            }
        });
    </script>
    <script>
        $(document).on('click', '.openRentModal', function() {

            const unit = $(this).data('units');
            const subunits = $(this).data('subunits');
            const unitRent = Number(unit?.total_rent_per_unit_per_month);
            const agreementUnitId = $(this).data('unit-id');
            const agreementId = $(this).data('agreement-id');
            const agreementUnits = agreement.agreement_units; // agreement must be defined globally
            const currentUnit = agreementUnits.find(u => u.id == agreementUnitId);
            console.log("currentUnit:", agreementUnits, currentUnit);
            const savedRents = currentUnit?.agreement_subunit_rent_bifurcation || [];
            console.log(savedRents);
            console.log(unitRent);
            console.log(subunits);
            console.log(unit);

            let rows = '';
            let runningTotal = 0;

            $('#totalUnitRent').text(unitRent.toFixed(2));
            subunits.forEach((subunit, index) => {

                // let value = unit.subunit_rent_per_unit;
                const saved = savedRents.find(r => r.contract_subunit_details_id === subunit.id);
                console.log("saved:", saved);
                const value = saved ? Number(saved.rent_per_month) : Number(unit?.subunit_rent_per_unit);
                console.log("value:", value);
                const split_id = saved ? saved.id : null;

                runningTotal += value;
                rows += `
                    <tr class="subunit-row" data-subunit-id="${subunit.id}" data-split-id="${split_id}">
                        <td>
                            ${subunit.subunit_no}
                            <input type="hidden"
                                name="subunits[${index}][id]"
                                value="${subunit.id}">
                        </td>
                        <td>
                            <input type="number"
                                step="0.01"
                                class="form-control subunit-rent"
                                name="subunits[${index}][rent]"
                                value="${value}">
                        </td>
                    </tr>
                `;
            });
            rows += `
                    <tr>
                        <td class="font-weight-bold text-right">
                            Total
                        </td>
                        <td id="sumSubunitRent" class="font-weight-bold">
                            ${runningTotal.toFixed(2)}
                        </td>
                    </tr>
                `;

            $('#bifurcationRows').html(rows);
            $('#bifurcationUnitId').val(unit.id);
            $('#agreementUnitId').val(agreementUnitId);
            $('#agreementId').val(agreementId);
            // $('#sumSubunitRent').text(runningTotal.toFixed(2));
            // Check total and enable/disable button
            toggleSubmitButton(unitRent);
            $('#rentBifurcationModal').modal('show');
        });
        $(document).on('input', '.subunit-rent', function() {
            let sum = 0;
            $('.subunit-rent').each(function() {
                sum += Number($(this).val()) || 0;
            });
            $('#sumSubunitRent').text(sum.toFixed(2));
            const totalUnitRent = Number($('#totalUnitRent').text());
            toggleSubmitButton(totalUnitRent);
        });
        // Function to toggle submit button
        function toggleSubmitButton(unitRent) {
            let sum = 0;
            $('.subunit-rent').each(function() {
                sum += Number($(this).val()) || 0;
            });

            if (sum.toFixed(2) == unitRent.toFixed(2)) {
                $('#saveRentBifurcation').prop('disabled', false);
                $('#rentMismatchError').hide();
            } else {
                $('#saveRentBifurcation').prop('disabled', true);
                $('#rentMismatchError').show();
            }
        }
    </script>
    <script>
        $('#rentBifurcationForm').on('submit', function(e) {
            e.preventDefault();

            // let totalUnitRent = parseFloat($('#totalUnitRent').text()) || 0;
            // let sumSubunitRent = 0;

            // // Calculate total of subunit rents
            // $('.subunit-rent-input').each(function() {
            //     sumSubunitRent += parseFloat($(this).val()) || 0;
            // });

            // // Validation: rent mismatch
            // if (sumSubunitRent.toFixed(2) !== totalUnitRent.toFixed(2)) {
            //     $('#rentMismatchError').show();
            //     return false;
            // } else {
            //     $('#rentMismatchError').hide();
            // }

            // Prepare subunit data
            let bifurcationData = [];

            $('.subunit-row').each(function() {
                bifurcationData.push({
                    contract_subunit_details_id: $(this).data('subunit-id'),
                    rent_per_month: $(this).find('.subunit-rent').val()
                });
                if ($(this).data('split-id')) {
                    bifurcationData[bifurcationData.length - 1].id = $(this).data('split-id');
                }
            });
            console.log(bifurcationData);

            let formData = {
                _token: $('input[name="_token"]').val(),
                agreement_id: $('#agreementId').val(),
                agreement_unit_id: $('#agreementUnitId').val(),
                contract_unit_details_id: $('#bifurcationUnitId').val(),
                bifurcation: bifurcationData
            };

            $.ajax({
                url: "{{ route('rent-bifurcation.store') }}",
                type: "POST",
                data: formData,
                success: function(response) {
                    $('#rentBifurcationModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Saved!',
                        text: response.message,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.reload();
                    });
                },
                error: function(xhr) {
                    toastr.error('Something went wrong');
                    console.error(xhr.responseText);
                }
            });
        });
    </script>
@endsection
