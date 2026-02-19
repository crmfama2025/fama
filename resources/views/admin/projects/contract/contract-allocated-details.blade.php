@extends('admin.layout.admin_master')

@section('custom_css')
    <link rel="stylesheet" href="{{ asset('assets/icheck-bootstrap/icheck-bootstrap.min.css') }}">

    <style>
        .contractTable tbody tr {
            background-color: #f6ffff;
        }

        .contractTable thead tr {
            background-color: #D6EEEE;
        }
    </style>
@endsection
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Contract Documents</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                            <li class="breadcrumb-item active">Contract Documents</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <!-- <h3 class="card-title">Contract Documents list</h3> -->
                                <span class="float-left">
                                    <strong>{{ 'P - ' . $contract->project_number }}</strong> </br>
                                    <span class="price-badge badge badge-danger">
                                        {{ $contract->contract_type->contract_type }} Project
                                    </span>
                                    <span class="price-badge badge badge-info">
                                        {{ $contract->contract_unit->business_type == 1 ? 'B2B' : 'B2C' }}
                                    </span><br>

                                    <a href="{{ route('vendors.show', $contract->vendor_id) }}" class="linkhover"
                                        target="_blank">
                                        {{ strtoupper($contract->vendor->vendor_name) }}
                                    </a><br>
                                </span>
                                <span class="float-right">
                                    <a href="{{ route('contract.show', $contract->id) }}"
                                        class="btn btn-info float-right m-1" target="_blank">View Contract</a>
                                    {{-- <button class="btn btn-info float-right m-1" data-toggle="modal"
                                        data-target="#modal-upload">Upload Files</button> --}}
                                </span>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                @php
                                    $mainData = unitDetailCount(
                                        $contractUnitdetails,
                                        $contract->contract_unit->business_type,
                                        1,
                                    );
                                @endphp
                                {{-- @foreach ($contractUnitdetails as $contractUnitdetail)
                                    @php
                                        $occupied += $contractUnitdetail->subunit_occupied_count;
                                        $vacant += $contractUnitdetail->subunit_vacant_count;
                                        $paymentReceived += $contractUnitdetail->total_payment_received;
                                        $payamentPending += $contractUnitdetail->total_payment_pending;
                                    @endphp
                                @endforeach --}}
                                <div class="row">
                                    <div class="col-md-3 col-sm-6 col-12">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-info"><i class="fas fa-lock"></i></span>

                                            <div class="info-box-content">
                                                <span class="info-box-text">Occupied</span>
                                                <span class="info-box-number">{{ $mainData['occupied'] }}</span>
                                            </div>
                                            <!-- /.info-box-content -->
                                        </div>
                                        <!-- /.info-box -->
                                    </div>
                                    <!-- /.col -->
                                    <div class="col-md-3 col-sm-6 col-12">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-success"><i class="fas fa-unlock"></i></span>

                                            <div class="info-box-content">
                                                <span class="info-box-text">Vacant</span>
                                                <span class="info-box-number">{{ $mainData['vacant'] }}</span>
                                            </div>
                                            <!-- /.info-box-content -->
                                        </div>
                                        <!-- /.info-box -->
                                    </div>
                                    <!-- /.col -->
                                    <div class="col-md-3 col-sm-6 col-12">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-warning"><i
                                                    class="fas fa-hand-holding-usd"></i></span>

                                            <div class="info-box-content">
                                                <span class="info-box-text">Payment Received</span>
                                                <span class="info-box-number">{{ $mainData['paymentReceived'] }}</span>
                                            </div>
                                            <!-- /.info-box-content -->
                                        </div>
                                        <!-- /.info-box -->
                                    </div>
                                    <!-- /.col -->
                                    <div class="col-md-3 col-sm-6 col-12">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-danger"><i
                                                    class="fas fa-funnel-dollar"></i></span>

                                            <div class="info-box-content">
                                                <span class="info-box-text">Payment Pending</span>
                                                <span class="info-box-number">{{ $mainData['paymentPending'] }}</span>
                                            </div>
                                            <!-- /.info-box-content -->
                                        </div>
                                        <!-- /.info-box -->
                                    </div>
                                    <!-- /.col -->
                                </div>



                                <br>
                                <!-- we are adding the accordion ID so Bootstrap's collapse plugin detects it -->
                                <div id="accordion">
                                    @php
                                        $unitdet = [];
                                    @endphp
                                    @if ($contract->contract_unit->business_type == 2)
                                        @foreach ($contractUnitdetails as $key => $contractUnitdetail)
                                            @include('admin.projects.contract.contract-agreement-view', [
                                                'unitNumbers' => $contractUnitdetail->unit_number,
                                                'agreementUnits' => $contractUnitdetail->agreementUnits,
                                                'unitdetail' => $contractUnitdetail,
                                                'businessType' => $contract->contract_unit->business_type,
                                            ])
                                        @endforeach
                                    @elseif($contract->contract_unit->business_type == 1)
                                        @foreach ($contract->agreements as $key => $agreement)
                                            @include('admin.projects.contract.contract-agreement-view', [
                                                'agreements' => $key + 1,
                                                'agreementUnits' => $agreement->agreement_units,
                                                'businessType' => $contract->contract_unit->business_type,
                                            ])
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->


    </div>
    <!-- /.modal -->

    </section>
    <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection

@section('custom_js')
    @include('admin.projects.contract.includes.contract_document_js')
@endsection
