@extends('layouts.master') @section('title', 'One JAF')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    .clock-in-btn {
        display: inline-block;
        margin-left: 10px;
    }

    .clock-in-btn form,
    .clock-in-btn button {
        display: inline-block;
        margin: 0;
    }

    /* Mobile View */
    @media (max-width: 600px) {
        .clock-in-btn {
            display: block;
            /* Stack elements vertically */
            margin-left: 0;
            /* Remove left margin */
            margin-bottom: 10px;
            /* Add space between buttons */
        }

        .clock-in-btn form,
        .clock-in-btn button {
            display: block;
            /* Make forms and buttons take full width */
            width: 100%;
            /* Full width for better accessibility */
            margin: 0;
            /* Reset margin */
        }
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
                                <a href="#">
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
                                    <!-- <div class="col-md-4">
                                        <div class="attendance-details">
                                            <h4 class="text-purple"></h4>
                                            <p>Vacation Leave</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="attendance-details">
                                            <h4 class="text-info"></h4>
                                            <p>Sick <br>Leave</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="attendance-details">
                                            <h4 class="text-danger"></h4>
                                            <p>Birthday Leave</p>
                                        </div>
                                    </div> -->
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
                            <a href="activities.html">
                                View All <i class="fe fe-arrow-right-circle"></i>
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
                            <li>
                                <a href="#" data-bs-toggle="tab" data-bs-target="#schedule_tab" aria-selected="false"
                                    tabindex="-1" role="tab">
                                    <i class="la la-list-alt"></i> Schedules
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="notification_tab" role="tabpanel">
                                <div class="employee-noti-content">
                                    <ul class="employee-notification-list">
                                        <li class="employee-notification-grid">
                                            <div class="employee-notification-icon">
                                                <a href="activities.html">
                                                    <span class="badge-soft-warning rounded-circle">SM</span>
                                                </a>
                                            </div>
                                            <div class="employee-notification-content">
                                                <h6>
                                                    <a href="activities.html">
                                                        Your annual compliance trai
                                                    </a>
                                                </h6>
                                                <ul class="nav">
                                                    <li>11:00 AM</li>
                                                    <li>21 Apr 2024</li>
                                                </ul>
                                            </div>
                                        </li>
                                        <li class="employee-notification-grid">
                                            <div class="employee-notification-icon">
                                                <a href="activities.html">
                                                    <span class="badge-soft-warning rounded-circle">DT</span>
                                                </a>
                                            </div>
                                            <div class="employee-notification-content">
                                                <h6>
                                                    <a href="activities.html">
                                                        Gentle remainder about train
                                                    </a>
                                                </h6>
                                                <ul class="nav">
                                                    <li>09:00 AM</li>
                                                    <li>21 Apr 2024</li>
                                                </ul>
                                            </div>
                                        </li>
                                        <li class="employee-notification-grid">
                                            <div class="employee-notification-icon">
                                                <a href="activities.html">
                                                    <span class="badge-soft-danger rounded-circle">AU</span>
                                                </a>
                                            </div>
                                            <div class="employee-notification-content">
                                                <h6>
                                                    <a href="activities.html">
                                                        Our HR system will be down
                                                    </a>
                                                </h6>
                                                <ul class="nav">
                                                    <li>11:50 AM</li>
                                                    <li>21 Apr 2024</li>
                                                </ul>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="schedule_tab" role="tabpanel">
                                <div class="employee-noti-content">
                                    <ul class="employee-notification-list">
                                        <li class="employee-notification-grid">
                                            <div class="employee-notification-icon">
                                                <a href="activities.html">
                                                    <span class="rounded-circle">
                                                        <img src="assets/img/avatar/avatar-2.jpg"
                                                            class="img-fluid rounded-circle" alt="User">
                                                    </span>
                                                </a>
                                            </div>
                                            <div class="employee-notification-content">
                                                <h6>
                                                    <a href="activities.html">
                                                        John has requested feedba
                                                    </a>
                                                </h6>
                                                <ul class="nav">
                                                    <li>10:30 AM</li>
                                                    <li>21 Apr 2024</li>
                                                </ul>
                                            </div>
                                        </li>
                                        <li class="employee-notification-grid">
                                            <div class="employee-notification-icon">
                                                <a href="activities.html">
                                                    <span class="badge-soft-danger rounded-circle">HR</span>
                                                </a>
                                            </div>
                                            <div class="employee-notification-content">
                                                <h6>
                                                    <a href="activities.html">
                                                        Your leave request has been
                                                    </a>
                                                </h6>
                                                <ul class="nav">
                                                    <li>02:10 PM</li>
                                                    <li>21 Apr 2024</li>
                                                </ul>
                                            </div>
                                        </li>
                                        <li class="employee-notification-grid">
                                            <div class="employee-notification-icon">
                                                <a href="activities.html">
                                                    <span class="badge-soft-info rounded-circle">ER</span>
                                                </a>
                                            </div>
                                            <div class="employee-notification-content">
                                                <h6>
                                                    <a href="activities.html">
                                                        You’re enrolled in upcom....
                                                    </a>
                                                </h6>
                                                <ul class="nav">
                                                    <li>12:40 PM</li>
                                                    <li>21 Apr 2024</li>
                                                </ul>
                                            </div>
                                        </li>
                                        <li class="employee-notification-grid">
                                            <div class="employee-notification-icon">
                                                <a href="activities.html">
                                                    <span class="badge-soft-warning rounded-circle">SM</span>
                                                </a>
                                            </div>
                                            <div class="employee-notification-content">
                                                <h6>
                                                    <a href="activities.html">
                                                        Your annual compliance trai
                                                    </a>
                                                </h6>
                                                <ul class="nav">
                                                    <li>11:00 AM</li>
                                                    <li>21 Apr 2024</li>
                                                </ul>
                                            </div>
                                        </li>
                                        <li class="employee-notification-grid">
                                            <div class="employee-notification-icon">
                                                <a href="activities.html">
                                                    <span class="badge-soft-warning rounded-circle">DT</span>
                                                </a>
                                            </div>
                                            <div class="employee-notification-content">
                                                <h6>
                                                    <a href="activities.html">
                                                        Gentle remainder about train
                                                    </a>
                                                </h6>
                                                <ul class="nav">
                                                    <li>09:00 AM</li>
                                                    <li>21 Apr 2024</li>
                                                </ul>
                                            </div>
                                        </li>
                                        <li class="employee-notification-grid">
                                            <div class="employee-notification-icon">
                                                <a href="activities.html">
                                                    <span class="badge-soft-danger rounded-circle">AU</span>
                                                </a>
                                            </div>
                                            <div class="employee-notification-content">
                                                <h6>
                                                    <a href="activities.html">
                                                        Our HR system will be down
                                                    </a>
                                                </h6>
                                                <ul class="nav">
                                                    <li>11:50 AM</li>
                                                    <li>21 Apr 2024</li>
                                                </ul>
                                            </div>
                                        </li>
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
                                <h4>Employee of the Month</h4>
                                <p>We are really proud of the difference you have made which gives everybody the reason
                                    to applaud &amp; appreciate.</p>
                            </div>
                            <div class="employee-month-content">
                                <h6><strong>Congrats, Hanna</strong></h6>
                                <p>UI/UX Team Lead</p>
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
                                <img src="https://smarthr.dreamstechnologies.com/laravel/template/public/assets/img/employee-img.png"
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
                        <form method="POST" action="">
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
                                <button type="submit" class="btn btn-primary submit-btn">Create</button>
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
                                <div class="owl-item cloned" style="width: 265.3px; margin-right: 20px;">
                                    <div class="company-grid company-soft-success">
                                        <div class="company-top">
                                            <div class="company-icon">
                                                <span class="company-icon-success rounded-circle">EP</span>
                                            </div>
                                            <div class="company-link">
                                                <a
                                                    href="https://smarthr.dreamstechnologies.com/laravel/template/public/companies">Employer
                                                    Policy</a>
                                            </div>
                                        </div>
                                        <div class="company-bottom d-flex">
                                            <ul>
                                                <li>Policy Name : Parking</li>
                                                <li>Updated on : 25 Jan 2024</li>
                                            </ul>
                                            <div class="company-bottom-links">
                                                <a href="#"><i class="la la-download"></i></a>
                                                <a href="#"><i class="la la-eye"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="owl-item cloned" style="width: 265.3px; margin-right: 20px;">
                                    <div class="company-grid company-soft-info">
                                        <div class="company-top">
                                            <div class="company-icon">
                                                <span class="company-icon-info rounded-circle">LP</span>
                                            </div>
                                            <div class="company-link">
                                                <a
                                                    href="https://smarthr.dreamstechnologies.com/laravel/template/public/companies">Leave
                                                    Policy</a>
                                            </div>
                                        </div>
                                        <div class="company-bottom d-flex">
                                            <ul>
                                                <li>Policy Name : Annual Leave</li>
                                                <li>Updated on : 25 Jan 2023</li>
                                            </ul>
                                            <div class="company-bottom-links">
                                                <a href="#"><i class="la la-download"></i></a>
                                                <a href="#"><i class="la la-eye"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="owl-item active" style="width: 265.3px; margin-right: 20px;">
                                    <div class="company-grid company-soft-tertiary">
                                        <div class="company-top">
                                            <div class="company-icon">
                                                <span class="company-icon-tertiary rounded-circle">HR</span>
                                            </div>
                                            <div class="company-link">
                                                <a
                                                    href="https://smarthr.dreamstechnologies.com/laravel/template/public/companies">HR
                                                    Policy</a>
                                            </div>
                                        </div>
                                        <div class="company-bottom d-flex">
                                            <ul>
                                                <li>Policy Name : Work policy</li>
                                                <li>Updated on : Today</li>
                                            </ul>
                                            <div class="company-bottom-links">
                                                <a href="#"><i class="la la-download"></i></a>
                                                <a href="#"><i class="la la-eye"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="owl-item active" style="width: 265.3px; margin-right: 20px;">
                                    <div class="company-grid company-soft-success">
                                        <div class="company-top">
                                            <div class="company-icon">
                                                <span class="company-icon-success rounded-circle">EP</span>
                                            </div>
                                            <div class="company-link">
                                                <a
                                                    href="https://smarthr.dreamstechnologies.com/laravel/template/public/companies">Employer
                                                    Policy</a>
                                            </div>
                                        </div>
                                        <div class="company-bottom d-flex">
                                            <ul>
                                                <li>Policy Name : Parking</li>
                                                <li>Updated on : 25 Jan 2024</li>
                                            </ul>
                                            <div class="company-bottom-links">
                                                <a href="#"><i class="la la-download"></i></a>
                                                <a href="#"><i class="la la-eye"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="owl-item" style="width: 265.3px; margin-right: 20px;">
                                    <div class="company-grid company-soft-info">
                                        <div class="company-top">
                                            <div class="company-icon">
                                                <span class="company-icon-info rounded-circle">LP</span>
                                            </div>
                                            <div class="company-link">
                                                <a
                                                    href="https://smarthr.dreamstechnologies.com/laravel/template/public/companies">Leave
                                                    Policy</a>
                                            </div>
                                        </div>
                                        <div class="company-bottom d-flex">
                                            <ul>
                                                <li>Policy Name : Annual Leave</li>
                                                <li>Updated on : 25 Jan 2023</li>
                                            </ul>
                                            <div class="company-bottom-links">
                                                <a href="#"><i class="la la-download"></i></a>
                                                <a href="#"><i class="la la-eye"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="owl-item cloned" style="width: 265.3px; margin-right: 20px;">
                                    <div class="company-grid company-soft-tertiary">
                                        <div class="company-top">
                                            <div class="company-icon">
                                                <span class="company-icon-tertiary rounded-circle">HR</span>
                                            </div>
                                            <div class="company-link">
                                                <a
                                                    href="https://smarthr.dreamstechnologies.com/laravel/template/public/companies">HR
                                                    Policy</a>
                                            </div>
                                        </div>
                                        <div class="company-bottom d-flex">
                                            <ul>
                                                <li>Policy Name : Work policy</li>
                                                <li>Updated on : Today</li>
                                            </ul>
                                            <div class="company-bottom-links">
                                                <a href="#"><i class="la la-download"></i></a>
                                                <a href="#"><i class="la la-eye"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="owl-item cloned" style="width: 265.3px; margin-right: 20px;">
                                    <div class="company-grid company-soft-success">
                                        <div class="company-top">
                                            <div class="company-icon">
                                                <span class="company-icon-success rounded-circle">EP</span>
                                            </div>
                                            <div class="company-link">
                                                <a
                                                    href="https://smarthr.dreamstechnologies.com/laravel/template/public/companies">Employer
                                                    Policy</a>
                                            </div>
                                        </div>
                                        <div class="company-bottom d-flex">
                                            <ul>
                                                <li>Policy Name : Parking</li>
                                                <li>Updated on : 25 Jan 2024</li>
                                            </ul>
                                            <div class="company-bottom-links">
                                                <a href="#"><i class="la la-download"></i></a>
                                                <a href="#"><i class="la la-eye"></i></a>
                                            </div>
                                        </div>
                                    </div>
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


@endsection
