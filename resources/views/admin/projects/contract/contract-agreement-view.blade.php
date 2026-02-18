<div class="card card-secondary">
    @if ($businessType == 2)
        <div class="card-header">
            <h4 class="card-title w-100 row">
                <a class="d-block w-100" data-toggle="collapse" href="#collapse{{ $key }}">
                    Unit
                    {{ $unitNumbers }}


                    <span class="badge badge-light float-lg-right"> Total Agreements
                        :
                        {{ count($agreementUnits) }}</span>
                </a>
            </h4>
        </div>
    @else
        <div class="card-header">
            <h4 class="card-title w-100 row">
                <a class="d-block w-100" data-toggle="collapse" href="#collapse{{ $key }}">
                    Agreement
                    {{ $agreements }}


                    <span class="badge badge-light float-lg-right"> Total Units
                        :
                        {{ count($agreementUnits) }}</span>
                </a>
            </h4>
        </div>

        @php
            $details = unitDetailCount($agreementUnits, $businessType, 2);
        @endphp
    @endif

    <div id="collapse{{ $key }}" class="collapse" data-parent="#accordion">
        <div class="card-body">
            <div class="col-12 table-responsive card-body">
                <div class="row">
                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="info-box">
                            <span class="bg-gradient-pink info-box-icon"><i class="fas fa-lock"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text">Occupied</span>
                                <span
                                    class="info-box-number">{{ $businessType == 2 ? $unitdetail->subunit_occupied_count : $details['occupied'] }}</span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>
                    <!-- /.col -->
                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="info-box">
                            <span class="bg-gradient-pink info-box-icon"><i class="fas fa-unlock"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text">Vacant</span>
                                <span
                                    class="info-box-number">{{ $businessType == 2 ? $unitdetail->subunit_vacant_count : $details['vacant'] }}</span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>
                    <!-- /.col -->
                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="info-box">
                            <span class="bg-gradient-pink info-box-icon"><i class="fas fa-hand-holding-usd"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text">Payment Received</span>
                                <span
                                    class="info-box-number">{{ $businessType == 2 ? $unitdetail->total_payment_received : $details['paymentReceived'] }}</span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>
                    <!-- /.col -->

                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="info-box">
                            <span class="bg-gradient-pink info-box-icon"><i class="fas fa-funnel-dollar"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text">Payment Pending</span>
                                <span
                                    class="info-box-number">{{ $businessType == 2 ? $unitdetail->total_payment_pending : $details['paymentPending'] }}</span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>
                    <!-- /.col -->
                </div>

                @if ($businessType == 1)
                    <div class="col-12 table-responsive card-body">
                        <div class="row">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Unit number</th>
                                        <th>Subunit Type</th>
                                        <th>Subunit Count</th>
                                        <th>Payment Pending</th>
                                        <th>Payment Received</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($agreementUnits as $agreementUnit)
                                        <tr>
                                            <td>{{ $agreementUnit->contractUnitDetail->unit_number }}</td>
                                            <td>{{ subunittypeName($agreementUnit->contractUnitDetail->subunittype) }}
                                            </td>
                                            <td>{{ $agreementUnit->contractUnitDetail->subunit_occupied_count + $agreementUnit->contractUnitDetail->subunit_vacant_count }}
                                            </td>
                                            <td>{{ $agreementUnit->contractUnitDetail->total_payment_pending }}</td>
                                            <td>{{ $agreementUnit->contractUnitDetail->total_payment_received }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif


                <div class="d-flex justify-content-center row">
                    {{-- @dd($agreementUnits[0]) --}}
                    @if ($businessType == 2)
                        @foreach ($agreementUnits as $agreementUnit)
                            @include('admin.projects.contract.includes.contract-tenant-details', [
                                'unitNumbers' => $contractUnitdetail->unit_number,
                                'agreementUnits' => $contractUnitdetail->agreementUnits,
                                'unitdetail' => $contractUnitdetail,
                            ])
                        @endforeach
                    @else
                        @include('admin.projects.contract.includes.contract-tenant-details', [
                            // 'unitNumbers' => $agreementUnit->unit_number,
                            'agreementUnit' => $agreementUnits[0],
                            // 'unitdetail' => $agreementUnit,
                        ])
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
