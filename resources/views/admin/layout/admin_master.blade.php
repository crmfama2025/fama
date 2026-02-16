<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>REAL ESTATE | CRM</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('assets/fontawesome-free/css/all.min.css') }}">
    <!-- IonIcons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Toastr -->
    <link rel="stylesheet" href="{{ asset('assets/toastr/toastr.min.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    @yield('custom_css')

    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('css/adminlte.css') }}">

    <!-- custom style -->
    <link rel="stylesheet" href="{{ asset('css/main.css') }}?v=3">
</head>
<!--
`body` tag options:

  Apply one or more of the following classes to to the body tag
  to get the desired effect

  * sidebar-collapse
  * sidebar-mini
-->

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar fixed-top navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i
                            class="fas fa-bars"></i></a>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <!-- Messages Dropdown Menu -->
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="fa fa-cog"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right"
                        style="max-height: -webkit-fill-available;">
                        <div class="card card-primary card-outline">
                            <div class="card-body box-profile">
                                <div class="text-center">
                                    <img class="profile-user-img img-fluid img-circle"
                                        src="{{ auth()->user()->profile_path ? asset('storage/' . auth()->user()->profile_path) : asset('img/profile1.png') }}"
                                        alt="User profile picture">
                                </div>

                                <h3 class="profile-username text-center">{{ auth()->user()->first_name }}
                                    {{ auth()->user()->last_name }}</h3>

                                <p class="text-muted text-center">{{ auth()->user()->user_type->user_type }}</p>

                                <ul class="list-group list-group-unbordered mb-3">
                                    <li class="list-group-item">
                                        <b>Contracts</b> <a
                                            class="float-right">{{ userAddedCount(\App\Models\Contract::class, auth()->user()->id) }}</a>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Investments</b> <a
                                            class="float-right">{{ userAddedCount(\App\Models\Investment::class, auth()->user()->id) }}</a>
                                    </li>
                                </ul>
                                <div class="mx-4 w-100">
                                    <a href="{{ route('user.profile') }}" class="btn btn-info"><i
                                            class="fa fa-cog"></i> Profile</a>
                                    <a class="btn btn-info" href="javascript:void(0)" onclick="signoutConf()"><i
                                            class="nav-icon fas fa-solid fa-arrow-right"></i>
                                        Signout</a>
                                </div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                    </div>
                </li>
                <!-- Notifications Dropdown Menu -->
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="far fa-bell"></i>
                        <span
                            class="badge badge-warning navbar-badge">{{ renewalCount() + statusCount(4) + getAgreementExpiringCounts() }}</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <span
                            class="dropdown-item dropdown-header">{{ renewalCount() + statusCount(4) + getAgreementExpiringCounts() }}
                            Notifications</span>
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('contract.renewal_pending_list') }}" class="dropdown-item">
                            <i class="fas fa-sync-alt mr-2"></i>{{ renewalCount() }} Renewal
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('contract.index', ['filter' => 'processing']) }}" class="dropdown-item">
                            <i class="fas fa-hourglass-half mr-2"></i>{{ statusCount(1) }} Send for Approval

                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('contract.index', ['filter' => 'approvalPending']) }}" class="dropdown-item">
                            <i class="fas fa-hourglass-half mr-2"></i>{{ statusCount(4) }} Approval Pending

                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('contract.index', ['filter' => 'approved']) }}" class="dropdown-item">
                            <i class="fas fa-hourglass-half mr-2"></i>{{ statusCount(2) }} Signature

                        </a>

                        <div class="dropdown-divider"></div>
                        <a href="{{ route('agreement.expiring-list') }}" class="dropdown-item">
                            <i class="fas fa-sync-alt mr-2"></i>{{ getAgreementExpiringCounts() }} Agreement Expiry
                        </a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                        <i class="fas fa-expand-arrows-alt"></i>
                    </a>
                </li>
                {{-- <li class="nav-item">
                    <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
                        <i class="fas fa-th-large"></i>
                    </a>
                </li> --}}
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4 position-fixed">
            <!-- Brand Logo -->
            <a href="{{ route('dashboard.index') }}" class="brand-link">
                <img src="{{ asset('images/fg.png') }}" class="brand-image">
                {{-- <img src="{{ asset('img/AdminLTELogo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8"> --}}
                <span class="brand-text font-weight-light">&nbsp</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar position-fixed" style="overflow-y: scroll;">
                <!-- Sidebar user panel (optional) -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <img src="{{ auth()->user()->profile_path ? asset('storage/' . auth()->user()->profile_path) : asset('img/profile1.png') }}"
                            class="img-circle elevation-2" alt="User Image">
                    </div>
                    <div class="info">
                        <a href="#" class="d-block">{{ auth()->user()->first_name }}
                            {{ auth()->user()->last_name }}</a>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">
                        <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                        <li class="nav-item">
                            <a href="{{ route('dashboard.index') }}"
                                class="nav-link {{ request()->is('dashboard') ? 'active bg-gradient-projects' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        @php
                            $master = $project = $finance = $invest = 0;
                            if (
                                request()->is([
                                    'areas',
                                    'locality',
                                    'property_type',
                                    'property',
                                    'vendors',
                                    'bank',
                                    'installment',
                                    'payment_mode',
                                    'nationality',
                                ])
                            ) {
                                $master = 1;
                            }
                            if (request()->is(['contract*', 'agreement*'])) {
                                $project = 1;
                            }

                            if (request()->is(['finance*'])) {
                                $finance = 1;
                            }

                            if (request()->is(['invest*']) || request()->is(['payout/*'])) {
                                $invest = 1;
                            }
                        @endphp
                        @if (auth()->user()->hasPermissionInRange(1, 45))
                            <li class="nav-item {{ $master ? 'menu-open' : '' }}">
                                <a href="#"
                                    class="nav-link {{ $master ? 'active bg-gradient-projects' : '' }}">
                                    <i class="nav-icon fas fa-copy"></i>
                                    <p>
                                        Masters
                                        <i class="fas fa-angle-left right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    @if (Gate::any(['Area', 'area.add', 'area.view', 'area.edit', 'area.delete']))
                                        <li class="nav-item">
                                            <a href="{{ route('areas.index') }}"
                                                class="nav-link {{ request()->is('areas') ? 'active' : '' }}">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Area</p>
                                            </a>
                                        </li>
                                    @endif
                                    @if (Gate::any(['Locality', 'locality.add', 'locality.view', 'locality.edit', 'locality.delete']))
                                        <li class="nav-item">
                                            <a href="{{ route('locality.index') }}"
                                                class="nav-link {{ request()->is('locality') ? 'active' : '' }}">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Locality</p>
                                            </a>
                                        </li>
                                    @endif
                                    {{-- @if (Gate::any(['Property_type', 'property_type.add', 'property_type.view', 'property_type.edit', 'property_type.delete']))
                                        <li class="nav-item">
                                            <a href="{{ route('property_type.index') }}"
                                                class="nav-link {{ request()->is('property_type') ? 'active' : '' }}">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Property Type</p>
                                            </a>
                                        </li>
                                    @endif --}}
                                    @if (Gate::any(['Property', 'property.add', 'property.view', 'property.edit', 'property.delete']))
                                        <li class="nav-item">
                                            <a href="{{ route('property.index') }}"
                                                class="nav-link {{ request()->is('property') ? 'active' : '' }}">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Property</p>
                                            </a>
                                        </li>
                                    @endif
                                    @if (Gate::any(['Vendor', 'vendor.add', 'vendor.view', 'vendor.edit', 'vendor.delete']))
                                        <li class="nav-item">
                                            <a href="{{ route('vendors.index') }}"
                                                class="nav-link {{ request()->is('vendors') ? 'active' : '' }}">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Vendor</p>
                                            </a>
                                        </li>
                                    @endif
                                    @if (Gate::any(['Bank', 'bank.add', 'bank.view', 'bank.edit', 'bank.delete']))
                                        <li class="nav-item">
                                            <a href="{{ route('bank.index') }}"
                                                class="nav-link {{ request()->is('bank') ? 'active' : '' }}">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Bank</p>
                                            </a>
                                        </li>
                                    @endif
                                    @if (Gate::any(['Installments', 'installments.add', 'installments.view', 'installments.edit', 'installments.delete']))
                                        <li class="nav-item">
                                            <a href="{{ route('installment.index') }}"
                                                class="nav-link {{ request()->is('installment') ? 'active' : '' }}">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Installment</p>
                                            </a>
                                        </li>
                                    @endif
                                    @if (Gate::any(['Payment_mode', 'payment_mode.add', 'payment_mode.view', 'payment_mode.edit', 'payment_mode.delete']))
                                        <li class="nav-item">
                                            <a href="{{ route('payment_mode.index') }}"
                                                class="nav-link {{ request()->is('payment_mode') ? 'active' : '' }}">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Payment Mode</p>
                                            </a>
                                        </li>
                                    @endif
                                    @if (Gate::any(['Nationality', 'nationality.add', 'nationality.view', 'nationality.edit', 'nationality.delete']))
                                        <li class="nav-item">
                                            <a href="{{ route('nationality.index') }}"
                                                class="nav-link {{ request()->is('nationality') ? 'active' : '' }}">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Nationality</p>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        @if (hasPermission(auth()->id(), ['contract', 'agreement'], $companyId = null))
                            {{-- auth()->user()->hasPermissionInRange(56, 77) ||
                                Gate::any(['contract.send_for_approval', 'contract.sign_after_approval'])) --}}
                            <li class="nav-item {{ $project ? 'menu-open' : '' }}">
                                <a href="#"
                                    class="nav-link {{ $project ? 'active bg-gradient-projects' : '' }}">
                                    <i class="nav-icon fas fa-edit"></i>
                                    <p>
                                        Project
                                        <i class="fas fa-angle-left right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    @if (hasPermission(auth()->id(), ['contract'], $companyId = null))
                                        <li class="nav-item">
                                            <a href="{{ route('contract.index') }}"
                                                class="nav-link {{ request()->is('contract*') ? 'active' : '' }}">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Contract</p>
                                            </a>
                                        </li>
                                    @endif
                                    @if (hasPermission(auth()->id(), ['agreement'], $companyId = null))
                                        <li class="nav-item">
                                            <a href="{{ route('agreement.index') }}"
                                                class="nav-link {{ request()->is('agreement*') ? 'active' : '' }}">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Agreement</p>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        @if (hasPermission(auth()->id(), ['finance'], $companyId = null))
                            <li class="nav-item {{ $finance ? 'menu-open' : '' }}">
                                <a href="#"
                                    class="nav-link {{ $finance ? 'active bg-gradient-projects' : '' }}">
                                    <i class="nav-icon fas fa-file-invoice"></i>
                                    <p>
                                        Finance
                                        <i class="fas fa-angle-left right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    @if (auth()->user()->hasAnyPermission(['finance.payable_cheque_clearing']))
                                        <li class="nav-item">
                                            <a href="{{ route('finance.payable.clearing') }}"
                                                class="nav-link {{ request()->is('finance/payable*') ? 'active' : '' }}">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Payables clearing</p>
                                            </a>
                                        </li>
                                    @endif
                                    @if (auth()->user()->hasAnyPermission(['finance.receivable_cheque_clearing']))
                                        <li class="nav-item">
                                            <a href="{{ route('tenant.cheque.clearing') }}"
                                                class="nav-link {{ request()->is('finance/receivable*') ? 'active' : '' }}">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Receivables clearing</p>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        @if (hasPermission(auth()->id(), ['investor', 'investment', 'finance'], $companyId = null))
                            <li class="nav-item {{ $invest ? 'menu-open' : '' }}">
                                <a href="#"
                                    class="nav-link {{ $invest ? 'active bg-gradient-projects' : '' }}">
                                    <i class="nav-icon fas fa-coins"></i>
                                    <p>
                                        Investment
                                        <i class="fas fa-angle-left right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    @if (hasPermission(auth()->id(), ['investor'], $companyId = null))
                                        <li class="nav-item ">
                                            <a href="{{ route('investor.index') }}"
                                                class="nav-link {{ request()->is('investor*') && !request()->is('investorPayout*') ? 'active' : '' }}">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Investors</p>
                                            </a>
                                        </li>
                                    @endif

                                    @if (hasPermission(auth()->id(), ['investment'], $companyId = null))
                                        <li class="nav-item">
                                            <a href="{{ route('investment.index') }}"
                                                class="nav-link {{ request()->is('investment*') && !request()->is('investments/referrals*') ? 'active' : '' }}">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Investments</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('referrals.index') }}"
                                                class="nav-link {{ request()->is('investments/referrals*') ? 'active' : '' }}">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Referrals</p>
                                            </a>
                                        </li>
                                    @endif
                                    @if (auth()->user()->hasAnyPermission(['finance.payout']))
                                        <li class="nav-item">
                                            <a href="{{ route('investorPayout.index') }}"
                                                class="nav-link {{ request()->is('investorPayout*') || request()->is('payout/*') ? 'active' : '' }}">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Investor Payout</p>
                                            </a>
                                        </li>
                                    @endif
                                    @if (auth()->user()->hasAnyPermission(['investment.soa']))
                                        <li class="nav-item">
                                            <a href="{{ route('investment-soa.list') }}"
                                                class="nav-link  {{ request()->is('investments/investment-soa') ? 'active' : '' }}">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Investment SOA</p>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        @if (auth()->user()->hasAnyPermission(['User', 'user.add', 'user.view', 'user.edit', 'user.delete']))
                            <li class="nav-item {{ request()->is('user') ? 'menu-open' : '' }}">
                                <a href="{{ route('user.index') }}"
                                    class="nav-link {{ request()->is('user*') ? 'active bg-gradient-projects' : '' }}">
                                    <i class="nav-icon fas fa-user"></i>
                                    <p>
                                        Users
                                    </p>
                                </a>
                            </li>
                        @endif
                        @if (auth()->user()->hasAnyPermission(['Company', 'company.add', 'company.view', 'company.edit', 'company.delete']))
                            <li class="nav-item {{ request()->is('company') ? 'menu-open' : '' }}">
                                <a href="{{ route('company.index') }}"
                                    class="nav-link {{ request()->is('company*') ? 'active bg-gradient-projects' : '' }}">
                                    <i class="nav-icon fas fa-building"></i>
                                    <p>
                                        Company
                                    </p>
                                </a>
                            </li>
                        @endif
                        {{-- onclick="signoutConf()" --}}
                        <li class="nav-item">
                            <a href="javascript:void(0)" onclick="signoutConf()" class="nav-link">
                                <i class="nav-icon fas fa-solid fa-arrow-right"></i>
                                <p>Sign out</p>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                    style="display: none;">
                                    @csrf
                                </form>
                            </a>
                        </li>
                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        @yield('content')

        <!-- Global Loader -->
        <div id="global-loader" style="display:none;">
            <img src="{{ asset('images/fama-loader-new.gif') }}" alt="Loading..." />
        </div>

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->

        <!-- Main Footer -->
        <footer class="main-footer">
            <strong>Copyright &copy; 2026 <a href="https://famainvestment.ae/"
                    target="_blank">famainvestment.ae</a>.</strong>
            All rights reserved.
            <div class="float-right d-none d-sm-inline-block">
                <b>Version</b> 1.0.0
            </div>
        </footer>
    </div>
    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->

    <!-- jQuery -->
    <script src="{{ asset('assets/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap -->
    <script src="{{ asset('assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- Toastr -->
    <script src="{{ asset('assets/toastr/toastr.min.js') }}"></script>
    <!-- Select2 -->
    <script src="{{ asset('assets/select2/js/select2.full.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.22.5/dist/sweetalert2.all.min.js"></script>

    {{-- <script>
        window.addEventListener("pageshow", function(event) {
            // If page was loaded from back/forward cache
            if (event.persisted ||
                (window.performance && window.performance.getEntriesByType("navigation")[0].type === "back_forward")
            ) {

                // Redirect to login
                window.location.href = "{{ route('login') }}";
            }
        });
    </script> --}}
    <script>
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000"
        };
    </script>
    <script>
        function enableEnterNavigation(formSelector) {
            $(formSelector).on('keydown', 'input, select, textarea', function(e) {
                if (e.key !== 'Enter') return; // only handle Enter

                e.preventDefault();
                const $current = $(this);

                // Handle Select2 fields
                if ($current.hasClass('select2-hidden-accessible')) {
                    const select2data = $current.data('select2');
                    if (!select2data.isOpen()) {
                        $current.select2('open'); // open dropdown on Enter
                        return;
                    }
                    // if dropdown is open, let Enter select option
                    return;
                }

                // Find all focusable elements in the form
                const $focusable = $(formSelector)
                    .find('input, select, textarea, button')
                    .filter(':visible:not([readonly]):not([disabled])');

                const index = $focusable.index(this);

                if (index > -1 && index + 1 < $focusable.length) {
                    $focusable.eq(index + 1).focus();
                } else if (index + 1 === $focusable.length) {
                    // last element → submit
                    $(formSelector).submit();
                }
            });
        }
    </script>
    @yield('custom_js')

    <!-- AdminLTE -->
    <script src="{{ asset('js/adminlte.js') }}"></script>

    <script>
        $(function() {

            $('.select2').select2()
        });

        function signoutConf() {
            Swal.fire({
                title: "Are you sure?",
                text: "You want to sign out!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, sign out!"
            }).then((result) => {
                if (result.isConfirmed) {
                    // $.ajax({
                    //     type: "POST",
                    //     url: '/logout',
                    //     data: {
                    //         _token: $('meta[name="csrf-token"]').attr('content')
                    //     },
                    //     dataType: "json",
                    //     success: function(response) {
                    //         // toastr.success(response.message);
                    //         window.location.href = '/login';
                    //     }
                    // });
                    document.getElementById('logout-form').submit();
                }
            });
        }

        function showLoader() {
            // title, msg
            // Swal.fire({
            //     title: title, //'Processing upload...'
            //     html: msg, //'Please wait while the documents are being uploaded.'
            //     allowOutsideClick: false,
            //     didOpen: () => {
            //         Swal.showLoading();
            //     }
            // });

            $('#global-loader').fadeIn(150);
        }

        function hideLoader() {
            // Swal.close();
            $('#global-loader').fadeOut(150);
        }

        function setInvalid(input, message) {
            $(input).addClass("is-invalid").removeClass("is-valid");
            if (message) {
                toastr.error(message);
            }

        }

        // helper: valid
        function setValid(input) {
            $(input).addClass("is-valid").removeClass("is-invalid");
        }

        function phoneValidation(ele, field_name, message = 0) {
            const PhoneRegex = /^[1-9][0-9]{9,14}$/;
            const value = $(ele).val();

            if (!PhoneRegex.test(value)) {
                isValid = false;

                if (message != 1) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Invalid Phone',
                        text: 'Enter ' + field_name + ' with country code (digits only, no +)',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2500,
                    });
                }

                setInvalid(ele, "");

                return isValid;

            } else {
                isValid = true;
                setValid(ele);

                return isValid;
            }
        }

        function validateLandline(input) {
            // alert('here');
            const value = $(input).val().trim();
            const uaeLandlineRegex = /^0[2-9]\d{7}$/;
            console.log('Validating landline:', value);

            if (value === '') {
                $(input).removeClass('is-invalid');
                return true;
            }

            if (!uaeLandlineRegex.test(value)) {
                $(input).addClass('is-invalid');
                // toastr.error('Enter a valid UAE landline number (e.g. 04XXXXXXX)');
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Phone',
                    text: 'Enter a valid UAE landline number (e.g. 04XXXXXXX)',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2500,
                });
                return false;
            } else {
                $(input).removeClass('is-invalid');
                return true;
            }
        }
    </script>
    {{-- <script>
        $(window).on('load', function() {
            setTimeout(function() {
                $('#global-loader').fadeOut(300);
            }, 1000);
        });
    </script> --}}



</body>

</html>
