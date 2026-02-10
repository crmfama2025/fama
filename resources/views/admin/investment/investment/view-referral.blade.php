@extends('admin.layout.admin_master')

@section('custom_css')
    <!-- daterange picker -->
    <link rel="stylesheet" href="{{ asset('assets/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="{{ asset('assets/select2/css/select2.min.css') }}">
    <!-- iCheck for checkboxes and radio inputs -->
    <link rel="stylesheet" href="{{ asset('assets/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <style>
        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
    </style>
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>{{ $title }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="../dashboard.php">Home</a></li>
                            <li class="breadcrumb-item active">{{ $title }}</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- Referral Details -->
        <section class="content">
            <div class="container-fluid">

                <!-- Referral Details Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title m-0"><i class="fas fa-id-badge"></i> Referral Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            {{-- <div class="col-md-6 mb-2">
                                <strong>Referral ID:</strong> {{ $referral->id }}
                            </div> --}}
                            <div class="col-md-6 mb-2">
                                <strong>Status:</strong>
                                @php
                                    if (($referral->referral_commission_frequency_id ?? null) == 1) {
                                        $status = $referral->referral_commission_status == 1 ? 'Expired' : 'Active';
                                    } else {
                                        $status = 'Active';
                                    }

                                    $class = $status === 'Active' ? 'badge-success' : 'badge-danger';
                                @endphp

                                <span class="badge {{ $class }}">
                                    {{ $status }}
                                </span>
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Commission:</strong> {{ $referral->referral_commission_perc ?? '-' }}%
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Commission Amount:</strong>
                                {{ number_format($referral->referral_commission_amount ?? 0, 2) }}
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Frequency:</strong>
                                {{ $referral->commissionFrequency->commission_frequency_name ?? '-' }}
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Referrer Investor Name:</strong>
                                <a href="{{ route('investor.show', $referral->referrer->id) }}"
                                    class="text-primary text-decoration-none fw-semibold" target="_blank">
                                    {{ $referral->referrer->investor_name ?? '-' }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Investor Details Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title m-0"><i class="fas fa-user"></i> Referred Investor Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <strong>Name:</strong>
                                @if ($referral->investor)
                                    <a href="{{ route('investor.show', $referral->investor->id) }}"
                                        class="text-primary text-decoration-none fw-semibold" target="_blank">
                                        {{ $referral->investor->investor_name }}
                                    </a>
                                @else
                                    -
                                @endif
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Code:</strong> {{ $referral->investor->investor_code ?? '-' }}
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Email:</strong> {{ $referral->investor->investor_email ?? '-' }}
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Phone:</strong> {{ $referral->investor->investor_mobile ?? '-' }}
                            </div>
                        </div>
                    </div>
                </div>


                <div class="card shadow-sm mt-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="card-title m-0"><i class="fas fa-building"></i> Investment Details</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $investment = $referral->investment;
                        @endphp

                        @if (!$investment)
                            <p class="text-muted">No investment found for this referral.</p>
                        @else
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <strong>Project / Company:</strong>
                                    <span class="text-muted">{{ $investment->company->company_name ?? '-' }}</span>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Invested Company:</strong>
                                    <span class="text-muted">{{ $investment->investedCompany->company_name ?? '-' }}</span>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Investment Amount:</strong>
                                    <span class="text-muted">{{ number_format($investment->investment_amount, 2) }}</span>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Investment Date:</strong>
                                    <span
                                        class="text-muted">{{ \Carbon\Carbon::parse($investment->investment_date)->format('d-m-Y') ?? '-' }}</span>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Profit Percentage:</strong>
                                    <span class="text-muted">{{ $investment->profit_perc ?? '-' }}%</span>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Maturity Date:</strong>
                                    <span
                                        class="text-muted">{{ \Carbon\Carbon::parse($investment->maturity_date)->format('d-m-Y') ?? '-' }}</span>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Profit Interval:</strong>
                                    <span
                                        class="text-muted">{{ $investment->profitInterval->profit_interval_name ?? '-' }}</span>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Payout Batch:</strong>
                                    <span class="text-muted">{{ $investment->payoutBatch->batch_name ?? '-' }}</span>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>



                <!-- Payout Distribution History -->
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title m-0"><i class="fas fa-history"></i> Payout History</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $payout = $referral->investorPayouts->first(); // only one payout
                        @endphp

                        @if (!$payout || $payout->investorPayoutDistribution->isEmpty())
                            <p class="text-muted">No payout found for this referral.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover table-striped" id="distributionHistory">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>#sl No</th>
                                            <th>Paid Date</th>
                                            <th>Amount Paid</th>
                                            {{-- <th>Paid By</th> --}}
                                            <th>Payment Mode</th>
                                            <th>Bank</th>
                                            <th>Cheque Number</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($payout->investorPayoutDistribution as $dist)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $dist->paid_date ?? '-' }}</td>
                                                <td>{{ number_format($dist->amount_paid, 2) }}</td>
                                                {{-- <td>{{ $dist->paidBy->name ?? '-' }}</td> --}}
                                                <td>{{ $dist->paymentMode->payment_mode_name ?? '-' }}</td>
                                                <td>{{ $dist->paidBank->bank_name ?? '-' }}</td>
                                                <td>{{ $dist->paid_cheque_number ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>





            </div>

        </section>

    </div>
@endsection
@section('custom_js')
    <script src="{{ asset('assets/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/moment/moment.min.js') }}"></script>
    <script src="{{ asset('assets/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
    <script src="{{ asset('assets/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('assets/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('assets/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#distributionHistory').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "pageLength": 10
            });
        });
    </script>
@endsection
