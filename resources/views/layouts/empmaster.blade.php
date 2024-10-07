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
    <meta name="keywords"
        content="admin, estimates, bootstrap, business, corporate, creative, management, minimal, modern, accounts, invoice, html5, responsive, CRM, Projects" />
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

    <link rel="stylesheet" href="{{
                asset('assets/fontawesome-free-6.4.0-web/css/all.min.css')
            }}" />

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/line-awesome/1.3.0/line-awesome/css/line-awesome.min.css" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />

    <!-- Select2 CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}" />

    <!-- Datetimepicker CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-datetimepicker.min.css') }}" />

    <!-- Datatable CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/dataTables.bootstrap4.min.css') }}" />

    <!-- Tagsinput CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css') }}">

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
    <!-- Google MAP API -->
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCoZSVkyGR645u4B_OOFmepLzrRBB8Hgmc&callback=initMap">
    </script>

</head>

<body onload="startTime()">
    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <!-- Header -->
        <div class="header">
            <!-- Logo -->
            <div class="header-left">
                <a href="{{ url('emp/dashboard') }}" class="logo">
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
            <!-- /Header Title -->
            <a id="mobile_btn" class="mobile_btn" href="#sidebar"><i class="fa fa-bars"></i></a>
            <!-- Header Menu -->
            <ul class="nav user-menu">
                <!-- Search -->
                <li class="nav-item">
                    <div class="top-nav-search">
                        <a href="javascript:void(0);" class="responsive-search">
                            <i class="fa fa-search"></i>
                        </a>
                        <form action="#">
                            <input class="form-control" type="text" placeholder="Search here" />
                            <button class="btn" type="submit">
                                <i class="fa fa-search"></i>
                            </button>
                        </form>
                    </div>
                </li>
                <!-- /Search -->

                <!-- Notifications -->
                <li class="nav-item dropdown">
                    <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
                        <i class="fa fa-bell-o"></i>
                        <span class="badge badge-pill" id="notification-count">
                            {{ auth()->user()->unreadNotifications->count() }}
                        </span>
                    </a>
                    <div class="dropdown-menu notifications">
                        <div class="topnav-dropdown-header">
                            <span class="notification-title">Notifications</span>
                            <a href="{{ route('notifications.clearemp') }}" class="clear-noti">Clear All</a>
                        </div>
                        <div class="noti-content">
                            <ul class="notification-list">
                                @foreach(auth()->user()->unreadNotifications as $notification)
                                @php
                                // Set dynamic variables based on notification type
                                $notificationType = class_basename($notification->type);
                                $data = $notification->data;
                                $userName = $data['employee_name'] ?? auth()->user()->name;
                                @endphp

                                <li class="notification-message">
                                    <a href="{{ isset($data['leave_type']) ? route('leave.searchemp') : (isset($data['total_worked']) ? url('emp/dashboard') : '#') }}"
                                        class="notification-link" data-id="{{ $notification->id }}">
                                        <div class="media">
                                            <span class="avatar">
                                                <!-- Display the requester's image -->
                                                <img alt="Profile Image"
                                                    src="{{ asset('images/' . ($notification->data['image'] ?? 'default.png')) }}" />
                                            </span>
                                            <div class="media-body">
                                                <p class="noti-details">
                                                    @if(isset($data['leave_type']) && isset($data['employee_name']))
                                                    <!-- Leave Request Notification -->
                                                    <span class="noti-title">{{ $userName }}</span> requested a leave:
                                                    <span class="noti-title">{{ $data['leave_type'] }}</span> from
                                                    <span>{{ $data['start_date'] }}</span> to
                                                    <span>{{ $data['end_date'] }}</span>.

                                                    @elseif(isset($data['leave_type']) &&
                                                    !isset($data['employee_name']))
                                                    <!-- Leave Request Approved Notification -->
                                                    Your leave request for <span
                                                        class="noti-title">{{ $data['leave_type'] }}</span> from
                                                    <span>{{ $data['start_date'] }}</span> to
                                                    <span>{{ $data['end_date'] }}</span> has been
                                                    <span class="noti-title">approved</span>.

                                                    @elseif($notificationType == 'AttendanceApprovedNotification' &&
                                                    isset($data['cutoff']))
                                                    <!-- Attendance Approved Notification -->
                                                    Your attendance for the cutoff period <span
                                                        class="noti-title">{{ $data['cutoff'] }}</span>
                                                    (from <span>{{ $data['start_date'] }}</span> to
                                                    <span>{{ $data['end_date'] }}</span>)
                                                    has been <span class="noti-title">approved</span>.

                                                    @elseif($notificationType == 'AttendanceDeclinedNotification')
                                                    <!-- Attendance Declined Notification -->
                                                    Your attendance has been <span class="noti-title">declined</span>.

                                                    @elseif(isset($data['total_worked']))
                                                    <!-- Attendance Submission Notification -->
                                                    <span class="noti-title">{{ $userName }}</span> submitted attendance
                                                    for
                                                    <span class="noti-title">{{ $data['cutoff'] ?? 'Unknown' }}</span>.
                                                    Worked Hours:
                                                    <span>{{ $data['total_worked'] }}</span>, Late Hours:
                                                    <span>{{ $data['total_late'] }}</span>.
                                                    @elseif($notificationType == 'PayslipGeneratedNotification')
                                                    Your payslip for the period from <span
                                                        class="noti-title">{{ $data['start_date'] }}</span> to
                                                    <span class="noti-title">{{ $data['end_date'] }}</span> in the year
                                                    <span class="noti-title">{{ $data['year'] }}</span> has been
                                                    generated.
                                                    @elseif($notificationType == 'OvertimeRequestStatusUpdated')
                                                    <!-- Overtime Request Status Updated Notification -->
                                                    Your overtime request for <span
                                                        class="noti-title">{{ $data['total_hours'] }}</span> hours on
                                                    <span>{{ $data['date'] }}</span> has been <span
                                                        class="noti-title">{{ $data['status'] }}</span>.
                                                    @endif
                                                </p>
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
                        <a class="dropdown-item" href="{{ route('emp.profile') }}">My Profile</a>
                        <!-- <a class="dropdown-item" href="settings.html">Settings</a> -->
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
                    <a class="dropdown-item" href="{{ route('emp.profile') }}">My Profile</a>
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
                        <li class="{{ Request::is('emp/dashboard') ? 'active':'' }}">
                            <a href="{{ url('emp/dashboard') }}"><i class="la la-home"></i>
                                <span>Main</span></a>
                        </li>
                        <!-- <li class="submenu">
                            <a href="#"><i class="la la-cube"></i>
                                <span> Apps</span>
                                <span class="menu-arrow"></span></a>
                            <ul style="display: none">
                                <li><a href="chat.html">Chat</a></li>
                                <li class="submenu">
                                    <a href="#"><span> Calls</span>
                                        <span class="menu-arrow"></span></a>
                                    <ul style="display: none">
                                        <li>
                                            <a href="voice-call.html">Voice Call</a>
                                        </li>
                                        <li>
                                            <a href="video-call.html">Video Call</a>
                                        </li>
                                        <li>
                                            <a href="outgoing-call.html">Outgoing Call</a>
                                        </li>
                                        <li>
                                            <a href="incoming-call.html">Incoming Call</a>
                                        </li>
                                    </ul>
                                </li>
                                <li><a href="events.html">Calendar</a></li>
                                <li>
                                    <a href="contacts.html">Contacts</a>
                                </li>
                                <li><a href="inbox.html">Email</a></li>
                                <li>
                                    <a href="file-manager.html">File Manager</a>
                                </li>
                            </ul>
                        </li> -->
                        <li class="menu-title">
                            <span>Employee</span>
                        </li>
                        <li class="{{ Request::is('emp/attendance') ? 'active':'' }}">
                            <a href="{{ url('emp/attendance') }}"><i class="la la-calendar"></i>
                                <span>Attendance</span></a>
                        </li>
                        <li class="{{ Request::is('emp/overtime') ? 'active':'' }}">
                            <a href="{{ url('emp/overtime') }}"><i class="la la-clock"></i>
                                <span>Overtime</span></a>
                        </li>
                        <li class="{{ Request::is('emp/leave') ? 'active':'' }}">
                            <a href="{{ url('emp/leave') }}"><i class="la la-rocket"></i>
                                <span>Leave</span></a>
                        </li>
                        <!-- <li>
                            <a href="tickets.html"><i class="la la-ticket"></i>
                                <span>Tickets</span></a>
                        </li> -->
                        <li class="menu-title">
                            <span>Payroll</span>
                        </li>
                        <li>
                            <a href="{{ url('emp/payslip') }}"><i class="la la-money"></i>
                                <span>Payslip</span></a>
                        </li>
                        <!-- <li>
                            <a href="policies.html"><i class="la la-file-pdf-o"></i>
                                <span>Policies</span></a>
                        </li> -->

                        <!-- <li class="menu-title">
                            <span>Performance</span>
                        </li>
                        <li class="submenu">
                            <a href="#"><i class="la la-graduation-cap"></i>
                                <span> Performance </span>
                                <span class="menu-arrow"></span></a>
                            <ul style="display: none">
                                <li>
                                    <a href="performance-indicator.html">
                                        Performance Indicator
                                    </a>
                                </li>
                                <li>
                                    <a href="performance.html">
                                        Performance Review
                                    </a>
                                </li>
                                <li>
                                    <a href="performance-appraisal.html">
                                        Performance Appraisal
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="#"><i class="la la-crosshairs"></i>
                                <span> Goals </span>
                                <span class="menu-arrow"></span></a>
                            <ul style="display: none">
                                <li>
                                    <a href="goal-tracking.html">
                                        Goal List
                                    </a>
                                </li>
                                <li>
                                    <a href="goal-type.html"> Goal Type </a>
                                </li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="#"><i class="la la-edit"></i>
                                <span> Training </span>
                                <span class="menu-arrow"></span></a>
                            <ul style="display: none">
                                <li>
                                    <a href="training.html">
                                        Training List
                                    </a>
                                </li>
                                <li>
                                    <a href="trainers.html"> Trainers</a>
                                </li>
                                <li>
                                    <a href="training-type.html">
                                        Training Type
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="promotion.html"><i class="la la-bullhorn"></i>
                                <span>Promotion</span></a>
                        </li>
                        <li>
                            <a href="resignation.html"><i class="la la-external-link-square"></i>
                                <span>Resignation</span></a> -->


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
    <!-- <script src="{{
                asset('assets/plugins/morris/morris.min.js')
            }}"></script>
    <script src="{{
                asset('assets/plugins/raphael/raphael.min.js')
            }}"></script>
    <script src="{{ asset('assets/js/chart.js') }}"></script> -->

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

    <script>
        document.getElementById('toggle_btn').addEventListener('click', function () {
            var logoImg = document.getElementById('logo-img');
            var currentSrc = logoImg.getAttribute('src');
            var newSrc = currentSrc.includes('OneJAFwhite.png') ? "{{ url('assets/img/togglelogo.png') }}" :
                "{{ asset('assets/img/OneJAFwhite.png') }}";
            logoImg.setAttribute('src', newSrc);
        });

    </script>

    @if (session('success'))
    <script>
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-right", // Or any position you prefer
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000", // 5 seconds
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
        toastr.success("{{ session('success') }}");

    </script>
    @endif

    @if (session('error'))
    <script>
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-right", // Or any position you prefer
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000", // 5 seconds
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
        toastr.error("{{ session('error') }}");

    </script>
    @endif


</body>

</html>
