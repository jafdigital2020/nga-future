<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Include SweetAlert CSS file -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.6/dist/sweetalert2.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.6/dist/sweetalert2.all.min.js"></script>

    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0" />
    <meta name="description" />
    <meta name="author" />
    <meta name="robots" content="noindex, nofollow" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>

    <!-- Bootstrap CSS -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" />
    <!-- Fontawesome CSS -->
    <link href="{{ asset('assets/css/font-awesome.min.css') }}" rel="stylesheet" />
    <!-- Lineawesome CSS -->
    <link href="{{ asset('assets/css/line-awesome.min.css') }}" rel="stylesheet" />
    <!-- Chart CSS -->
    <link href="{{ asset('assets/plugins/morris/morris.css') }}" rel="stylesheet" />
    <!----FAVICON---->
    <link rel="icon" href="{{ url('assets/img/jaffavicon.png') }}" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{
                asset('assets/fontawesome-free-6.4.0-web/css/all.min.css')
            }}" />
    <!-- Line Awesome -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/line-awesome/1.3.0/line-awesome/css/line-awesome.min.css" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <!-- Select2 CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}" />
    <!-- Datetimepicker CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-datetimepicker.min.css') }}" />
    <!-- Datatable CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/dataTables.bootstrap4.min.css') }}" />
    <!-- New -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/feather2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/ow.carousel.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/material.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/feather.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <!-- Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"
        integrity="sha512-vKMx8UnXk60zUwyUnUPM3HbQo8QfmNx7+ltw8Pm5zLusl1XIfwcxo8DbWCqMGKaWeNxWA8yrx5v3SaVpMvR3CA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    @stack('styles')
</head>

<body onload="startTime()">
    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <!-- Header -->
        <div class="header">
            <!-- Logo -->
            <div class="header-left">
                <a href="{{ url('admin/dashboard') }}" class="logo">
                    <img class="logoSide" id="logo-img" src="{{ asset('assets/img/OneJAFwhite.png') }}"
                        alt="OneJAF Logo" width="100%" />
                </a>
            </div>
            <!-- /Logo -->
            <a id="toggle_btn" href="javascript:void(0);">
                <span class="bar-icon">
                    <span></span>
                    <span></span>
                    <span></span>
                </span>
            </a>
            <!-- Header Title -->
            <div class="page-title-box">
                <h3>{{ optional($themeSettings)->webName ?? '' }}</h3>
            </div>

            <!-- /Settings Redirection -->

            <!-- /Header Title -->
            <a id="mobile_btn" class="mobile_btn" href="#sidebar"><i class="fa fa-bars"></i></a>
            <!-- Header Menu -->
            <ul class="nav user-menu">


                <!-- Notifications -->
                <li class="nav-item dropdown">
                    <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
                        <i class="fa fa-bell-o"></i>
                        <span class="badge badge-pill"
                            id="notification-count">{{ auth()->user()->unreadNotifications->count() }}</span>
                    </a>
                    <div class="dropdown-menu notifications">
                        <div class="topnav-dropdown-header">
                            <span class="notification-title">Notifications</span>
                            <a href="{{ route('notifications.clear') }}" class="clear-noti">Clear All</a>
                        </div>
                        <div class="noti-content">
                            <ul class="notification-list">
                                @foreach(auth()->user()->unreadNotifications as $notification)
                                <li class="notification-message">
                                    <a href="{{ isset($notification->data['leave_type']) ? route('leave.searchadmin') : (isset($notification->data['total_worked']) ? route('approvedTime') : (isset($notification->data['missed_logout']) ? route('missedLogouts') : '#')) }}"
                                        class="notification-link" data-id="{{ $notification->id }}">
                                        <div class="media">
                                            <span class="avatar">
                                                <!-- Display the requester's image -->
                                                <img alt="Profile Image"
                                                    src="{{ asset('images/' . ($notification->data['image'] ?? 'default.png')) }}" />
                                            </span>
                                            <div class="media-body">
                                                @if(isset($notification->data['leave_type']) &&
                                                isset($notification->data['employee_name']))
                                                <!-- Leave Request Notification -->
                                                <p class="noti-details">
                                                    <span
                                                        class="noti-title">{{ $notification->data['employee_name'] }}</span>
                                                    requested a leave:
                                                    <span
                                                        class="noti-title">{{ $notification->data['leave_type'] }}</span>
                                                    from <span>{{ $notification->data['start_date'] }}</span> to
                                                    <span>{{ $notification->data['end_date'] }}</span>.
                                                </p>
                                                @elseif(isset($notification->data['total_worked']) &&
                                                isset($notification->data['employee_name']))
                                                <!-- Attendance Submission Notification -->
                                                <p class="noti-details">
                                                    <span
                                                        class="noti-title">{{ $notification->data['employee_name'] }}</span>
                                                    submitted attendance for
                                                    <span class="noti-title">{{ $notification->data['cutoff'] }}</span>.
                                                    Worked Hours:
                                                    <span>{{ $notification->data['total_worked'] }}</span>,
                                                    Late Hours: <span>{{ $notification->data['total_late'] }}</span>.
                                                </p>
                                                @elseif(isset($notification->data['leave_type']) &&
                                                !isset($notification->data['employee_name']))
                                                <!-- Leave Request Approved Notification -->
                                                <p class="noti-details">
                                                    Your leave request for
                                                    <span
                                                        class="noti-title">{{ $notification->data['leave_type'] }}</span>
                                                    from <span>{{ $notification->data['start_date'] }}</span> to
                                                    <span>{{ $notification->data['end_date'] }}</span> has been
                                                    approved.
                                                </p>
                                                @elseif(isset($notification->data['missed_logout']))
                                                <!-- Missed Logout Notification -->
                                                <p class="noti-details">
                                                    <span
                                                        class="noti-title">{{ $notification->data['employee_name'] }}</span>
                                                    missed logging out on
                                                    <span>{{ $notification->data['date'] }}</span>.
                                                </p>
                                                @endif
                                                <p class="noti-time">
                                                    <span
                                                        class="notification-time">{{ $notification->created_at->diffForHumans() }}</span>
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="topnav-dropdown-footer">
                            <a href="#">View all Notifications</a>
                        </div>
                    </div>
                </li>
                <!-- /Notifications -->


                <!-- Message Notifications -->
                <!-- <li class="nav-item dropdown">
                    <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
                        <i class="fa fa-comment-o"></i>
                        <span class="badge badge-pill">8</span>
                    </a>
                    <div class="dropdown-menu notifications">
                        <div class="topnav-dropdown-header">
                            <span class="notification-title">Messages</span>
                            <a href="javascript:void(0)" class="clear-noti">
                                Clear All
                            </a>
                        </div>
                        <div class="noti-content">
                            <ul class="notification-list">
                                <li class="notification-message">
                                    <a href="chat.html">
                                        <div class="list-item">
                                            <div class="list-left">
                                                <span class="avatar">
                                                    <img alt="" src="assets/img/profiles/avatar-09.jpg" />
                                                </span>
                                            </div>
                                            <div class="list-body">
                                                <span class="message-author">Richard Miles
                                                </span>
                                                <span class="message-time">12:28 AM</span>
                                                <div class="clearfix"></div>
                                                <span class="message-content">Lorem ipsum dolor sit
                                                    amet, consectetur
                                                    adipiscing</span>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li class="notification-message">
                                    <a href="chat.html">
                                        <div class="list-item">
                                            <div class="list-left">
                                                <span class="avatar">
                                                    <img alt="" src="assets/img/profiles/avatar-02.jpg" />
                                                </span>
                                            </div>
                                            <div class="list-body">
                                                <span class="message-author">John Doe</span>
                                                <span class="message-time">6 Mar</span>
                                                <div class="clearfix"></div>
                                                <span class="message-content">Lorem ipsum dolor sit
                                                    amet, consectetur
                                                    adipiscing</span>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li class="notification-message">
                                    <a href="chat.html">
                                        <div class="list-item">
                                            <div class="list-left">
                                                <span class="avatar">
                                                    <img alt="" src="assets/img/profiles/avatar-03.jpg" />
                                                </span>
                                            </div>
                                            <div class="list-body">
                                                <span class="message-author">
                                                    Tarah Shropshire
                                                </span>
                                                <span class="message-time">5 Mar</span>
                                                <div class="clearfix"></div>
                                                <span class="message-content">Lorem ipsum dolor sit
                                                    amet, consectetur
                                                    adipiscing</span>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li class="notification-message">
                                    <a href="chat.html">
                                        <div class="list-item">
                                            <div class="list-left">
                                                <span class="avatar">
                                                    <img alt="" src="assets/img/profiles/avatar-05.jpg" />
                                                </span>
                                            </div>
                                            <div class="list-body">
                                                <span class="message-author">Mike Litorus</span>
                                                <span class="message-time">3 Mar</span>
                                                <div class="clearfix"></div>
                                                <span class="message-content">Lorem ipsum dolor sit
                                                    amet, consectetur
                                                    adipiscing</span>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li class="notification-message">
                                    <a href="chat.html">
                                        <div class="list-item">
                                            <div class="list-left">
                                                <span class="avatar">
                                                    <img alt="" src="assets/img/profiles/avatar-08.jpg" />
                                                </span>
                                            </div>
                                            <div class="list-body">
                                                <span class="message-author">
                                                    Catherine Manseau
                                                </span>
                                                <span class="message-time">27 Feb</span>
                                                <div class="clearfix"></div>
                                                <span class="message-content">Lorem ipsum dolor sit
                                                    amet, consectetur
                                                    adipiscing</span>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="topnav-dropdown-footer">
                            <a href="chat.html">View all Messages</a>
                        </div>
                    </div>
                </li> -->
                <!-- /Message Notifications -->

                <li class="nav-item dropdown has-arrow main-drop">
                    <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
                        <span class="user-img"><img
                                src="{{ Auth::user()->image ? asset('images/' . Auth::user()->image) : asset('images/default.png') }}"
                                alt="Profile Image" />
                            <span class="status online"></span></span>
                        <span>{{ Auth::user()->name }}</span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="{{ url('admin/profile') }}">My Profile</a>
                        <a class="dropdown-item" href="{{ url('admin/settings') }}">Settings</a>
                        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                            {{ __("Logout") }}
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </li>
            </ul>
            <!-- /Header Menu -->

            <!-- Mobile Menu -->
            <div class="dropdown mobile-user-menu">
                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i
                        class="fa fa-ellipsis-v"></i></a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="{{ url('admin/profile') }}">My Profile</a>
                    <a class="dropdown-item" href="{{ url('admin/settings') }}">Settings</a>
                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                    document.getElementById('logout-form').submit();">
                        {{ __("Logout") }}
                    </a>
                </div>
            </div>
            <!-- /Mobile Menu -->
        </div>
        <!-- /Header -->

        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-inner slimscroll">
                <div id="sidebar-menu" class="sidebar-menu">
                    <ul>
                        <li class="menu-title">
                            <span>Main</span>
                        </li>
                        <li class="{{ Request::is('admin/dashboard') ? 'active':'' }}">
                            <a href="{{ url('admin/dashboard') }}"><i class="la la-home"></i>
                                <span>Dashboard</span></a>
                        </li>

                        <li class="menu-title">
                            <span>Admin</span>
                        </li>

                        <li class="{{ Request::is('admin/employee-grid') ? 'active':'' }}">
                            <a href="{{ url('admin/employee-grid') }}"
                                class="{{ Request::is('admin/employee-grid') ? 'active':'' }}"><i
                                    class="la la-user-secret"></i>
                                <span>Employees</span></a>
                        </li>
                        <li class="{{ Request::is('admin/leave') ? 'active':'' }}">
                            <a href="{{ url('admin/leave') }}"><i class="la la-rocket"></i>
                                <span>Leave</span></a>
                        </li>
                        <li class="{{ Request::is('admin/attendance') ? 'active':'' }}">
                            <a href="{{ url('admin/attendance') }}"><i class="la la-calendar"></i>
                                <span>Attendance</span></a>
                        </li>
                        <li class="{{ Request::is('admin/timesheet') ? 'active':'' }}">
                            <a href="{{ url('admin/timesheet') }}"><i class="las la-calendar-check"></i>
                                <span>Timesheet Approval</span></a>
                        </li>

                        <li class="menu-title">
                            <span>Payroll</span>
                        </li>
                        <li class="submenu">
                            <a href="#"><i class="la la-money"></i>
                                <span> Payroll </span>
                                <span class="menu-arrow"></span></a>
                            <ul style="display: none">
                                <li class="{{ Request::is('admin/approve') ? 'active':'' }}">
                                    <a href="{{ url('admin/approve') }}">
                                        Approved Timesheet
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ url('admin/processed') }}">
                                        Processed Timesheet
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ url('admin/approved/payslip') }}">
                                        Approved Payslip
                                    </a>
                                </li>
                                <li class="{{ Request::is('admin/payslip') ? 'active':'' }}">
                                    <a href="{{ url('admin/payslip') }}"> Generated Payslip </a>
                                </li>

                            </ul>
                        </li>
                        <li class="{{ Request::is('admin/policy') ? 'active':'' }}">
                            <a href="{{ url('admin/policy') }}"><i class="la la-file-pdf-o"></i>
                                <span>Policies</span></a>
                        </li>
                        <!-- 
                        <li class="menu-title">
                            <span>Training</span>
                        </li>
                        <li class="submenu">
                            <a href="#"><i class="la la-edit"></i> <span> Training </span> <span
                                    class="menu-arrow"></span></a>
                            <ul style="display: none;">
                                <li class="{{ Request::is('admin/training') ? 'active':'' }}">
                                    <a href="{{ url('admin/training') }}"> Training List </a>
                                </li>
                                <li class="{{ Request::is('admin/trainers') ? 'active':'' }}">
                                    <a href="{{ url('admin/trainers') }}"> Trainers</a>
                                </li>
                                <li class="{{ Request::is('admin/training-type') ? 'active':'' }}">
                                    <a href="{{ url('admin/training-type') }}">Training Type </a>
                                </li>
                            </ul>
                        </li>
                        <li class="menu-title">
                            <span>Sales & Accounting</span>
                        </li>
                        <li class="submenu">
                            <a href="#"><i class="la la-files-o"></i> <span> Sales </span> <span
                                    class="menu-arrow"></span></a>
                            <ul style="display: none;">
                                <li><a href="{{ route('admin.estimate') }}">Estimates</a></li>
                                <li><a href="{{ route('admin.invoice') }}">Invoices</a></li>
                                <li><a href="{{ route('admin.payment') }}">Payments</a></li>
                                <li><a href="{{ route('admin.expense') }}">Expenses</a></li>
                                <li><a href="{{ route('admin.tax') }}">Taxes</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="#"><i class="la la-files-o"></i> <span> Accounting </span> <span
                                    class="menu-arrow"></span></a>
                            <ul style="display: none;">
                                <li><a href="{{ route('admin.categories') }}">Categories</a></li>
                                <li><a href="budgets.html">Budgets</a></li>
                                <li><a href="budget-expenses.html">Budget Expenses</a></li>
                                <li><a href="budget-revenues.html">Budget Revenues</a></li>
                            </ul>
                        </li> -->

                    </ul>
                </div>
            </div>
        </div>
        <!-- /Sidebar -->

        <!-- Page Wrapper -->
        <div class="page-wrapper">
            <!-- Page Content -->
            @yield('content')
            <!-- /Page Content -->
        </div>
        <!-- /Page Wrapper -->
    </div>
    @include('sweetalert::alert')

    <!-- /Main Wrapper -->

    <!-- jQuery -->
    <script src="{{ asset('assets/js/jquery-3.5.1.min.js') }}"></script>

    <!-- Bootstrap Core JS -->
    <script src="{{ asset('assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>

    <!-- Slimscroll JS -->
    <script src="{{
                asset('assets/js/jquery.slimscroll.min.js')
            }}"></script>

    <!-- Chart JS -->
    <script src="{{
                asset('assets/plugins/morris/morris.min.js')
            }}"></script>
    <script src="{{
                asset('assets/plugins/raphael/raphael.min.js')
            }}"></script>
    <script src="{{ asset('assets/js/chart.js') }}"></script>

    <!-- Custom JS -->
    <script src="{{ asset('assets/js/app.js') }}"></script>

    <!-- Select2 JS -->
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>

    <!-- Datetimepicker JS -->
    <script src="{{ asset('assets/js/moment.min.js') }}"></script>
    <script src="{{
                asset('assets/js/bootstrap-datetimepicker.min.js')
            }}"></script>

    <!-- Datatable JS -->
    <script src="{{
                asset('assets/js/jquery.dataTables.min.js')
            }}"></script>
    <script src="{{
                asset('assets/js/dataTables.bootstrap4.min.js')
            }}"></script>

    <!-- EditTable -->
    <script src="{{asset('assets/js/edittable.js')}}"></script>


    <!-- Place jsPDF and any related scripts before the closing body tag -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- Carousel Policy -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>

    @yield('scripts')
    <script>
        $(".datetimepicker").datetimepicker({
            format: "YYYY-MM-DD",
            // Additional options if needed
        });

    </script>

    @stack('scripts')

</body>

</html>
