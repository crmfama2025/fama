@extends('admin.layout.admin_master')

@section('custom_css')
    <!-- daterange picker -->
    <link rel="stylesheet" href="{{ asset('assets/daterangepicker/daterangepicker.css') }}">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="{{ asset('assets/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endsection

@section('content')
    <!-- Content Wrapper. Contains page content -->
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

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <!-- <h3 class="card-title">Property Details</h3> -->
                                <span class="float-right">
                                    <a class="btn btn-info float-right m-1" href="{{ route('investor.create') }}">Add
                                        Investor</a>
                                </span>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="InvestorList" class="table table-bordered table-hover display nowrap">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Action</th>
                                            <th>Investor Details</th>
                                            <th>Nationality</th>
                                            <th>Country of Recidence</th>
                                            <th>Reference</th>
                                            {{-- <th>Email</th> --}}
                                            {{-- <th>Address</th> --}}
                                            <th>Emirates ID/ Passport No</th>
                                            <th>Payment Mode</th>
                                            {{-- <th>Bank Details</th> --}}
                                            <!-- <th>IBAN</th> -->
                                            {{-- <th>Mobile</th> --}}

                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- <tr>
                                            <td>1</td>
                                            <td> Mr. Ahmad Atieh Abdul Mohsen Sobuh</td>
                                            <td>Jordan</td>
                                            <td>UAE</td>
                                            <td>Reference 1</td>
                                            <td>asubah@eim.ae</td>
                                            <td>Al Majaz 3, Sharjah, UAE</td>
                                            <td>784-1971-0973742-5</td>
                                            <td>Bank Transfer</td>
                                            <td>Dubai Islamic Bank</td>
                                            <!-- <td>AE860240009580614910301</td> -->
                                            <td>+97150 450 0456</td>
                                            <td>
                                                <button class="btn btn-info" data-toggle="modal"
                                                    data-target="#modal-Property">Edit</button>
                                                <button class="btn btn-danger" onclick="deleteConf()">Delete</button>
                                            </td>
                                        </tr> --}}
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->


            <div class="modal fade" id="modal-import">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Import</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="" id="PropertyImportForm" method="POST" enctype="multipart/form-data">
                            <div class="modal-body">
                                <div class="card-body">
                                    <div class="form-group row">
                                        <label for="inputEmail3" class="col-sm-3 col-form-label">Import excel</label>
                                        <input type="file" name="file" class="col-sm-9 form-control">
                                    </div>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="button" id="importBtn" class="btn btn-info">Import</button>
                            </div>
                        </form>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>



            <!-- /.modal -->
            <!-- /.modal -->

        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection


@section('custom_js')
    <!-- Select2 -->
    <script src="{{ asset('assets/select2/js/select2.full.min.js') }}"></script>

    <script src="{{ asset('assets/moment/moment.min.js') }}"></script>
    <script src="{{ asset('assets/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
    <script src="{{ asset('assets/daterangepicker/daterangepicker.js') }}"></script>
    <!-- DataTables  & Plugins -->
    <script src="{{ asset('assets/datatables/jquery.dataTables.min.js') }}"></script>
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

    @include('admin.investment.investor-bank-modal')

    <script>
        $(function() {
            let table = $('#InvestorList').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,

                ajax: {
                    url: "{{ route('investor.list') }}",
                    data: function(d) {
                        // d.date_from = $('#date_From').val();
                        // d.date_to = $('#date_To').val();
                        // d.vendor_id = $('#vendor_id').val();
                        // d.property_id = $('#property_id').val();
                        // d.payment_mode = $('#payment_mode').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'id',
                        orderable: true,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'investor_name',
                        name: 'investor_name',
                    },
                    {
                        data: 'nationality_name',
                        name: 'nationality.nationality_name',
                    },
                    {
                        data: 'country_of_residence',
                        name: 'countryOfResidence.nationality_name',
                    },
                    {
                        data: 'referral',
                        name: 'referral.investor_name',
                    },
                    // {
                    //     data: 'investor_email',
                    //     name: 'investor_email',
                    // },
                    // {
                    //     data: 'investor_address',
                    //     name: 'investor_address',
                    // },
                    {
                        data: 'id_number',
                        name: 'id_number',
                    },
                    {
                        data: 'payment_mode',
                        name: 'paymentMode.payment_mode_name',
                    },
                    // {
                    //     data: 'investor_bank_name',
                    //     name: 'investorBanks.investor_bank_name',
                    // },
                    // {
                    //     data: 'investor_mobile',
                    //     name: 'investor_mobile',
                    // },

                ],
                rowCallback: function(row, data, index) {
                    // Example: Highlight pending payments
                    console.log(data.has_returned);
                    if (data.has_returned === 1) {
                        console.log(data.has_returned);
                        $(row).css('background-color', '#ffe1e1'); // light red
                    }

                },
                order: [
                    [0, 'desc']
                ],
                dom: 'Bfrtip', // This is important for buttons
                buttons: [{
                    text: 'Export Excel',
                    action: function(e, dt, node, config) {

                        let searchValue = dt.search();
                        let url = "{{ route('investor.export') }}";

                        if (searchValue) {
                            url += '?search=' + encodeURIComponent(searchValue);
                        }

                        window.location.href = url; // THIS triggers the route
                    }
                }]
            });


            $('.searchbtnchq').on('click', function(e) {
                e.preventDefault();
                table.ajax.reload();
            });

        });



        function deleteConf(id) {
            Swal.fire({
                title: "Are you sure?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "DELETE",
                        url: '/investor/' + id,
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: "json",
                        success: function(response) {
                            toastr.success(response.message);
                            $('#InvestorList').DataTable().ajax.reload();
                        }
                    });

                }
                // else {
                //     toastr.error(errors.responseJSON.message);
                // }
            });
        }
    </script>
@endsection
