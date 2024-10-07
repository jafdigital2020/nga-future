@extends('layouts.master') @section('title', 'One JAF')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    a.mobile_btn {
        margin-top: 20px;
    }

    .mobile-user-menu {
        display: block;
        margin-top: 25px;
    }

    .logoSide {
        margin-top: 0px;
    }

</style>

@section('content')

<div class="content container-fluid">

    <!-- Greetings & Notifcation Card  -->

    <div class="row">
        <div class="col-xxl-8 col-lg-12 col-md-12">
            <div class="row">
                <div class="col-lg-6 col-md-12">
                    <div class="card employee-welcome-card flex-fill">
                        <div class="card-body">
                            <div class="welcome-info">
                                <div class="welcome-content">
                                    <h4> Welcome, {{ Auth::user()->fName }} {{ Auth::user()->lName }}</h4>
                                    <p>You have <span>{{ auth()->user()->unreadNotifications->count() }}
                                            notifications</span> today,</p>
                                </div>
                                <div class="welcome-img">
                                    <img src="{{ Auth::user()->image ? asset('images/' . Auth::user()->image) : asset('images/default.png') }}"
                                        class="img-fluid" alt="User">
                                </div>
                            </div>
                            <div class="welcome-btn">
                                <a href="{{ url('admin/profile') }}" class="btn">View Profile</a>
                            </div>
                        </div>
                    </div>
                    <div class="card flex-fill">
                        <div class="card-body">
                            @php
                            // Ensure the percentage is between 0 and 100
                            $progressBarWidth = min(max($percentageIncrease, 0), 100);
                            $formattedPercentage = number_format($percentageIncrease, 2);
                            @endphp
                            <div class="d-flex justify-content-between mb-3">
                                <div>
                                    <span class="d-block">New Employees</span>
                                </div>
                                <div>
                                    <span class="text-success">
                                        {{ $percentageIncrease > 0 ? '+' : '' }}{{ $formattedPercentage }}%
                                    </span>
                                </div>
                            </div>
                            <h3 class="mb-3">{{ $newUsersThisMonth }}</h3>
                            <div class="progress mb-2" style="height: 5px;">
                                <div class="progress-bar bg-primary" role="progressbar"
                                    style="width: {{ $progressBarWidth }}%;" aria-valuenow="{{ $percentageIncrease }}"
                                    aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                            <p class="mb-0">Overall Employees: {{ $totalUsers }}</p>
                            <hr style="color:#D3D3D4;">
                            <div class="view-attendance">
                                <a href="{{ url('admin/employee-grid') }}">
                                    View Employees <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12">
                    <div class="card flex-fill">
                        <div class="card-body">
                            <div class="statistic-header">
                                <h4>Attendance</h4>
                                <div class="dropdown statistic-dropdown">
                                    <a class="dropdown-toggle" data-bs-toggle="dropdown" href="javascript:void(0);"
                                        aria-expanded="false">
                                        2024
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end" style="">
                                        <a href="javascript:void(0);" class="dropdown-item">
                                            2025
                                        </a>
                                        <a href="javascript:void(0);" class="dropdown-item">
                                            2026
                                        </a>
                                        <a href="javascript:void(0);" class="dropdown-item">
                                            2027
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="attendance-list">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="attendance-details">
                                            <h4 class="text-primary">
                                                {{ $todayLoginCount }}
                                            </h4>
                                            <p>Present Today</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="attendance-details">
                                            <h4 class="text-pink">
                                                {{ $vacationLeaveCountToday + $sickLeaveCountToday + $birthdayLeaveCountToday + $unpaidLeaveCountToday }}
                                            </h4>
                                            <p>Leave Today</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="attendance-details">
                                            <h4 class="text-success">{{ $totalLateToday }}</h4>
                                            <p>Late<br>Today</p>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="view-attendance">
                                <a href="{{ url('admin/attendance/') }}">
                                    View Attendance <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card info-card flex-fill" style="margin-top:-15px;">
                        <div class="card-body">
                            <h4 class="holiday-title">Upcoming Holidays</h4>
                            <div class="holiday-details">
                                <div class="holiday-calendar">
                                    <div class="holiday-calendar-icon">
                                        <i class="fa-solid fa-calendar-days" style="color:white; font-size: 30px;"></i>
                                    </div>
                                    <div class="holiday-calendar-content">
                                        @if($nearestHoliday)
                                        <h6>{{ $nearestHoliday->title }}</h6>
                                        <p>{{ $nearestHoliday->holidayDay }} {{ $nearestHoliday->holidayDate }}</p>
                                        @else
                                        <h6>No Holiday Set</h6>
                                        <p>No data found.</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="holiday-btn">
                                    <a href="{{ url('admin/settings/holiday') }}" class="btn">View</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="col-xxl-4 col-lg-12 col-md-12 d-flex">
            <div class="card flex-fill">
                <div class="card-body">
                    <div class="statistic-header">
                        <h4>Important</h4>
                        <div class="important-notification">
                            <a href="#">
                                View All <i class="fa fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="notification-tab">
                        <ul class="nav nav-tabs" role="tablist">
                            <li>
                                <a href="#" class="active" data-bs-toggle="tab" data-bs-target="#notification_tab"
                                    aria-selected="true" role="tab">
                                    <i class="la la-bell"></i> Notifications
                                </a>
                            </li>
                            <!-- <li>
                                <a href="#" data-bs-toggle="tab" data-bs-target="#schedule_tab" aria-selected="false"
                                    tabindex="-1" role="tab">
                                    <i class="la la-list-alt"></i> Schedules
                                </a>
                            </li> -->
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="notification_tab" role="tabpanel">
                                <div class="employee-noti-content">
                                    <ul class="employee-notification-list">
                                        @foreach(auth()->user()->unreadNotifications->take(3) as $notification)
                                        <li class="employee-notification-grid">
                                            <div class="employee-notification-icon">
                                                <a
                                                    href="{{ isset($notification->data['leave_type']) ? route('leave.searchadmin') : (isset($notification->data['total_worked']) ? route('approvedTime') : (isset($notification->data['missed_logout']) ? route('missedLogouts') : '#')) }}">
                                                    <span class="badge-soft-warning rounded-circle">
                                                        <!-- Display initials based on the employee's name or notification type -->
                                                        @if(isset($notification->data['employee_name']))
                                                        {{ strtoupper(substr($notification->data['employee_name'], 0, 2)) }}
                                                        @else
                                                        NT
                                                        @endif
                                                    </span>
                                                </a>
                                            </div>
                                            <div class="employee-notification-content">
                                                <h6>
                                                    <a
                                                        href="{{ isset($notification->data['leave_type']) ? route('leave.searchadmin') : (isset($notification->data['total_worked']) ? route('approvedTime') : (isset($notification->data['missed_logout']) ? route('missedLogouts') : '#')) }}">
                                                        @if(isset($notification->data['leave_type']) &&
                                                        isset($notification->data['employee_name']))
                                                        {{ $notification->data['employee_name'] }} requested leave:
                                                        {{ $notification->data['leave_type'] }}.
                                                        @elseif(isset($notification->data['total_worked']) &&
                                                        isset($notification->data['employee_name']))
                                                        {{ $notification->data['employee_name'] }} submitted attendance:
                                                        {{ $notification->data['cutoff'] }}.
                                                        @elseif(isset($notification->data['leave_type']) &&
                                                        !isset($notification->data['employee_name']))
                                                        Your leave request for {{ $notification->data['leave_type'] }}
                                                        has been approved.
                                                        @elseif(isset($notification->data['missed_logout']))
                                                        {{ $notification->data['employee_name'] }} missed logging out on
                                                        {{ $notification->data['date'] }}.
                                                        @endif
                                                    </a>
                                                </h6>
                                                <ul class="nav">
                                                    <li>{{ $notification->created_at->format('h:i A') }}</li>
                                                    <li>{{ $notification->created_at->format('d M Y') }}</li>
                                                </ul>
                                            </div>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Announcement and Policies -->

    <div class="row">
        <div class="col-xl-6 col-md-12 d-flex">
            <div class="card employee-month-card flex-fill">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-lg-9 col-md-12">
                            <div class="employee-month-details">
                                <h4>{{ $latestAnnouncement->annTitle ?? 'No Announcement is Displayed' }}</h4>
                                <p>{{$latestAnnouncement->annDescription ?? ''}}</p>
                            </div>
                            <div class="employee-month-content">
                                <h6><strong>{{ $latestAnnouncement->poster->fName ?? '' }}</strong></h6>
                                <p>{{ $latestAnnouncement->poster->position ?? '' }}</p>
                            </div>
                            <!-- Add the button here -->
                            <div class="employee-month-button mt-3">
                                <!-- Button trigger modal -->
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#create_announcement">
                                    Create New Announcement
                                </button>

                            </div>
                        </div>
                        <div class="col-lg-3 col-md-12">
                            <div class="employee-month-img">
                                <img src="{{ asset('images/' . ($latestAnnouncement->annImage ?? 'default.png')) }}"
                                    class="img-fluid" alt="User">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--Add Announcement Modal -->
        <div id="create_announcement" class="modal custom-modal fade" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Create Announcement</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="{{ route('admin.announcement') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="">Title</label>
                                <input type="text" class="form-control" name="annTitle" id="annTitle">
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <textarea class="form-control" name="annDescription" id="annDescription"></textarea>
                            </div>
                            <div class="form-group">
                                <label>Attach image
                                    <input name="annImage" type="file" accept="image/*" />
                                </label>
                            </div>
                            <div class="submit-section">
                                <button type="submit" class="btn btn-primary submit-btn">Post</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Add Announcement Modal -->


        <div class="col-xl-6 col-md-12 d-flex">
            <div class="card flex-fill">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-sm-8">
                            <div class="statistic-header">
                                <h4>Company Policy</h4>
                            </div>
                        </div>
                        <div class="col-sm-4 text-sm-end">
                            <div class="owl-nav company-nav nav-control"><button type="button" role="presentation"
                                    class="owl-prev"><i class="fa-solid fa-chevron-left"></i></button><button
                                    type="button" role="presentation" class="owl-next"><i
                                        class="fa-solid fa-chevron-right"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="company-slider owl-carousel owl-loaded owl-drag">
                        <div class="owl-stage-outer">
                            <div class="owl-stage"
                                style="transform: translate3d(-570px, 0px, 0px); transition: all; width: 1998px;">
                                <div class="owl-carousel owl-theme">
                                    @foreach ($policies as $policy)
                                    <div class="owl-item" style="width: 265.3px; margin-right: 20px;">
                                        <div class="company-grid company-soft-success">
                                            <div class="company-top">
                                                <div class="company-icon">
                                                    <span class="company-icon-success rounded-circle">EP</span>
                                                </div>
                                                <div class="company-link">
                                                    <a href="#">{{ $policy->policyTitle }}</a>
                                                </div>
                                            </div>
                                            <div class="company-bottom d-flex">
                                                <ul>
                                                    <li>Policy Name : {{ $policy->policyName }}</li>
                                                    <li>Updated on : {{ $policy->updated_at->format('d M Y') }}</li>
                                                </ul>
                                                <div class="company-bottom-links">
                                                    @if ($policy->policyUpload)
                                                    <a href="{{ asset('storage/' . $policy->policyUpload) }}"
                                                        target="_blank">
                                                        <i class="la la-eye"></i>
                                                    </a>
                                                    @else
                                                    <span></span>
                                                    @endif
                                                    <a href="#"
                                                        onclick="downloadPolicy('{{ asset('storage/' . $policy->policyUpload) }}')">
                                                        <i class="la la-download"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="owl-dots disabled"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        // Initialize the Owl Carousel
        var $carousel = $('.company-slider').owlCarousel({
            items: 2, // Number of items to show
            loop: true, // Enable looping
            margin: 20, // Margin between items
            nav: false, // Disable default navigation
            dots: false // Disable dots navigation
        });

        // Bind click events to your custom navigation buttons
        $('.owl-prev').on('click', function () {
            $carousel.trigger('prev.owl.carousel');
        });
        $('.owl-next').on('click', function () {
            $carousel.trigger('next.owl.carousel');
        });
    });

</script>

<script>
    function downloadPolicy(url) {
        if (url) {
            window.open(url, '_blank');
        } else {
            alert('No document available for download');
        }
    }

</script>

<script>
    function startTime() {
        const today = new Date();
        let h = today.getHours();
        let m = today.getMinutes();
        let s = today.getSeconds();
        let ampm = h >= 12 ? 'PM' : 'AM';
        h = h % 12;
        h = h ? h : 12; // the hour '0' should be '12'
        m = checkTime(m);
        s = checkTime(s);
        document.getElementById('clock').innerHTML = h + ":" + m + ":" + s + " " + ampm;
        setTimeout(startTime, 1000);
    }

    function checkTime(i) {
        if (i < 10) {
            i = "0" + i
        }; // add zero in front of numbers < 10
        return i;
    }

</script>

<script>
    $(document).ready(function () {
        $('#attendance_info').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var employee = button.data('employee');
            var date = button.data('date');
            var timeIn = button.data('timein');
            var timeOut = button.data('timeout');
            var breakIn = button.data('breakin');
            var breakEnd = button.data('breakend');
            var breakOut = button.data('breakout');
            var timeTotal = button.data('timetotal');
            // Update the modal's content
            var modal = $(this);
            modal.find('.modal-title').text('Attendance Info for ' + employee);
            modal.find('.card-title small').text(date);
            modal.find('.punch-det:first p').text(timeIn);
            modal.find('.punch-det:last p').text(timeOut);
            modal.find('.statistics .stats-box:first h6').text(breakEnd);
            modal.find('#time-total').text(timeTotal);

            // Populate activities list
            var activities = `
                <li>
                    <p class="mb-0">Punch In at</p>
                    <p class="res-activity-time">
                        <i class="fa fa-clock-o"></i>
                        ${timeIn}
                    </p>
                </li>
                
                <li>
                    <p class="mb-0">Break In at</p>
                    <p class="res-activity-time">
                        <i class="fa fa-clock-o"></i>
                        ${breakIn}
                    </p>
                </li>
                <li>
                    <p class="mb-0">Break Out at</p>
                    <p class="res-activity-time">
                        <i class="fa fa-clock-o"></i>
                        ${breakOut}
                    </p>
                </li>
                <li>
                    <p class="mb-0">Punch Out at</p>
                    <p class="res-activity-time">
                        <i class="fa fa-clock-o"></i>
                        ${timeOut}
                    </p>
                </li>
            `;
            modal.find('.res-activity-list').html(activities);
        });
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

@endsection
