@extends('layouts.empmaster') @section('title', 'Dashboard')
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

    <div class="content container-fluid pb-0">

        <div class="row">
            <div class="col-md-12">
                <div class="employee-alert-box">
                </div>
            </div>
        </div>

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
                                    <a href="{{ url('emp/profile') }}" class="btn">View Profile</a>
                                </div>
                            </div>
                        </div>
                        <div class="card flex-fill">
                            <div class="card-body">
                                <div class="statistic-header">
                                    <h4>Statistics</h4>
                                    <div class="dropdown statistic-dropdown">

                                        <a class="text-muted" id="clock">

                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a href="javascript:void(0);" class="dropdown-item">
                                                Week
                                            </a>
                                            <a href="javascript:void(0);" class="dropdown-item">
                                                Month
                                            </a>
                                            <a href="javascript:void(0);" class="dropdown-item">
                                                Year
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="clock-in-info">
                                    <div class="clock-in-content">
                                        <p>Work Time</p>
                                        <h4>
                                            @if ($latest && $latest->date === now()->format('Y-m-d'))
                                                <span>{{ $latest->timeTotal }}</span>
                                            @else
                                                <span>00:00:00</span>
                                            @endif
                                        </h4>
                                    </div>
                                    <div class="clock-in-btn">
                                        <form id="clockInForm" action="{{ url('emp/dashboard') }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="latitude" id="latitude">
                                            <input type="hidden" name="longitude" id="longitude">
                                            <input type="hidden" name="location" id="location">
                                            <button type="button" class="btn btn-warning"
                                                id="checkInButton">Clock-In</button>
                                        </form>
                                    </div>

                                    <div id="imageUploadModal" class="modal fade" tabindex="-1" role="dialog"
                                        aria-labelledby="imageUploadModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="imageUploadModalLabel">Capture a Photo</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form id="imageUploadForm">
                                                        <div class="form-group">
                                                            <label>Your location accuracy is low. Please capture a photo to
                                                                complete clock-in.</label>
                                                            <!-- Video element to show camera feed -->
                                                            <video id="video" autoplay style="width: 100%;"></video>
                                                            <!-- Canvas element for capturing photo -->
                                                            <canvas id="canvas" style="display: none;"></canvas>
                                                            <!-- Button to capture photo -->
                                                            <button type="button" class="btn btn-primary mt-2"
                                                                id="captureButton">Capture Photo</button>
                                                            <!-- Preview captured image -->
                                                            <img id="capturedImage"
                                                                style="display: none; width: 100%; margin-top: 10px;" />
                                                        </div>
                                                    </form>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-primary"
                                                        id="submitImage">Submit</button>
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">Cancel</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="clock-in-btn">
                                        <button type="submit" class="btn btn-outline-warning" data-toggle="modal"
                                            data-target="#exampleModal">
                                            Clock Out
                                        </button>
                                    </div>

                                    <!--Clock Out Modal 1 -->
                                    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog"
                                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel">
                                                        Warning!
                                                    </h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    Are you sure you want to time out?
                                                </div>
                                                <div class="modal-footer">
                                                    <form id="clockOutForm" action="{{ url('emp/dashboard/clockout') }}"
                                                        method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        <input type="hidden" name="latitude" id="outLatitude">
                                                        <input type="hidden" name="longitude" id="outLongitude">
                                                        <input type="hidden" name="location" id="outLocation">
                                                        <button type="button" class="btn btn-outline-warning"
                                                            id="clockOutButton">Clock Out</button>
                                                    </form>
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">
                                                        No
                                                    </button>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Clock Out Modal 1 -->

                                    <!-- Clock Out Image Modal -->
                                    <div id="clockOutImageModal" class="modal fade" tabindex="-1" role="dialog"
                                        aria-labelledby="clockOutImageModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="clockOutImageModalLabel">Capture a Photo
                                                    </h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form id="clockOutImageUploadForm">
                                                        <div class="form-group">
                                                            <label>Your location accuracy is low. Please capture a photo to
                                                                complete clock-out.</label>
                                                            <!-- Video element for camera feed -->
                                                            <video id="outVideo" autoplay style="width: 100%;"></video>
                                                            <!-- Canvas element for capturing photo -->
                                                            <canvas id="outCanvas" style="display: none;"></canvas>
                                                            <!-- Capture Button -->
                                                            <button type="button" class="btn btn-primary mt-2"
                                                                id="outCaptureButton">Capture Photo</button>
                                                            <!-- Preview captured image -->
                                                            <img id="outCapturedImage"
                                                                style="display: none; width: 100%; margin-top: 10px;" />
                                                        </div>
                                                    </form>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-primary"
                                                        id="outSubmitImage">Submit</button>
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">Cancel</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- CLOCK-OUT IMAGE -->
                                </div>
                                <div class="clock-in-list mt-4">
                                    <ul class="nav">
                                        <!-- Break Options Dropdown for 1st and 2nd 15 Minutes Break -->
                                        <div class="dropdown mx-2">
                                            <button class="btn btn-danger btn-sm dropdown-toggle" type="button"
                                                id="breakDropdown" data-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false" style="width: 200px;">
                                                Select Break
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="breakDropdown">
                                                <!-- Start 1 Hour Break -->
                                                <form action="{{ url('emp/dashboard/breakin/') }}" method="POST">
                                                    @csrf @method('PUT')
                                                    <button type="submit" class="dropdown-item"
                                                        id="start1HourBreak">Start 1
                                                        Hour Break</button>
                                                </form>
                                                <!-- End 1 Hour Break -->
                                                <form action="{{ url('emp/dashboard/breakout/') }}" method="POST">
                                                    @csrf @method('PUT')
                                                    <button type="submit" class="dropdown-item" id="end1HourBreak">End 1
                                                        Hour Break</button>
                                                </form>
                                                <!-- Start 15 Minutes Break -->
                                                <form action="{{ route('emp.startBreak') }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item"
                                                        id="start15MinBreak">Start
                                                        15 Minutes Break</button>
                                                </form>
                                                <!-- End 15 Minutes Break -->
                                                <form action="{{ route('emp.endBreak') }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item" id="end15MinBreak">End
                                                        15
                                                        Minutes Break</button>
                                                </form>
                                            </div>
                                        </div>
                                        <li>
                                            <p>Break Timer</p>
                                            <h6 id="countdown">00:00:00</h6>
                                        </li>
                                    </ul>
                                </div>


                                <div class="clock-in-info">
                                    <div class="clock-in-content">
                                        <p>Overtime</p>
                                        <h4>
                                            @if ($latest && $latest->date === now()->format('Y-m-d'))
                                                <span>{{ $latest->timeTotal }}</span>
                                            @else
                                                <span>00:00:00</span>
                                            @endif
                                        </h4>
                                    </div>
                                    <div class="clock-in-btn">
                                        <form id="overtimeInForm" action="{{ url('emp/dashboard/overtime') }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <button type="button" class="btn btn-warning" id="overtimeInButton">Overtime-In</button>
                                        </form>
                                    </div>

                                    <div class="clock-in-btn">
                                        <form id="overtimeOutForm" action="{{ url('emp/dashboard/overtime') }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <button type="button" class="btn btn-outline-warning" id="overtimeOutButton">Overtime-Out</button>
                                        </form>
                                    </div>
                                    

                                </div>

                                <div class="view-attendance">
                                    <a href="#">
                                        View Attendance <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12">
                        <div class="card flex-fill">
                            <div class="card-body">
                                <div class="statistic-header">
                                    <h4>Attendance &amp; Leaves</h4>
                                    <div class="dropdown statistic-dropdown">
                                        <a class="dropdown-toggle" data-bs-toggle="dropdown" href="javascript:void(0);"
                                            aria-expanded="false">
                                            2025
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
                                                    {{ $totalLeaveCredits ?? 0 }}
                                                </h4>
                                                <p>Total Leaves</p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="attendance-details">
                                                <h4 class="text-pink">{{ $leaveApproved + $attendanceApproved ?? 0 }}</h4>
                                                <p>Leaves Taken</p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="attendance-details">
                                                <h4 class="text-success">{{ $leavePending ?? 0 }}</h4>
                                                <p>Pending Approval</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="view-attendance">
                                    <a href="{{ url('emp/leave') }}">
                                        Apply Leave <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card info-card flex-fill">
                            <div class="card-body">
                                <h4 class="holiday-title">Upcoming Holidays</h4>
                                <div class="holiday-details">
                                    <div class="holiday-calendar">
                                        <div class="holiday-calendar-icon">
                                            <i class="fa-solid fa-calendar-days"
                                                style="color:white; font-size: 30px;"></i>
                                        </div>
                                        <div class="holiday-calendar-content">
                                            @if ($nearestHoliday)
                                                <h6>{{ $nearestHoliday->title }}</h6>
                                                <p>{{ $nearestHoliday->holidayDay }} {{ $nearestHoliday->holidayDate }}
                                                </p>
                                            @else
                                                <h6>No Holiday Set</h6>
                                                <p>No data found.</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="holiday-btn">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="col-xxl-4 col-lg-12 col-md-12 d-flex">
                <div class="card flex-fill recent-activity">
                    <div class="card-body">
                        <h5 class="card-title">Today's Activity</h5>
                        <ul class="res-activity-list">
                            @if ($latest && $latest->date === now()->format('Y-m-d'))
                                @if ($latest->timeIn)
                                    <li>
                                        <p class="mb-0">Punch In at</p>
                                        <p class="res-activity-time">
                                            <i class="fa fa-clock-o"></i>
                                            {{ $latest->timeIn }}
                                        </p>
                                    </li>
                                    <!-- Start -->
                                    @if (is_array($breaks) && count($breaks) > 0)
                                        @foreach ($breaks as $index => $break)
                                            <li>
                                                <p class="mb-0">15-Min Break {{ $index + 1 }} Start</p>
                                                <p class="res-activity-time">
                                                    <i class="fa fa-clock-o"></i>
                                                    {{ $break['start'] ?? 'N/A' }}
                                                </p>
                                            </li>
                                            @if (isset($break['end']))
                                                <li>
                                                    <p class="mb-0">15-Min Break {{ $index + 1 }} End</p>
                                                    <p class="res-activity-time">
                                                        <i class="fa fa-clock-o"></i>
                                                        {{ $break['end'] }}
                                                    </p>
                                                </li>
                                            @endif
                                        @endforeach
                                    @else
                                        <li>
                                            <p class="text-muted">No breaks recorded for today.</p>
                                        </li>
                                    @endif
                                    <!-- END -->
                                    @endif @if ($latest->breakIn)
                                        <li>
                                            <p class="mb-0">Break Out at</p>
                                            <p class="res-activity-time">
                                                <i class="fa fa-clock-o"></i>
                                                {{ $latest->breakIn }}
                                            </p>
                                        </li>
                                        @endif @if ($latest->breakOut)
                                            <li>
                                                <p class="mb-0">Break In at</p>
                                                <p class="res-activity-time">
                                                    <i class="fa fa-clock-o"></i>
                                                    {{ $latest->breakOut }}
                                                </p>
                                            </li>
                                            @endif
                                            @if ($latestOvertime  && $latestOvertime->start_time !==null)
                                            <li>
                                                <p class="mb-0">Overtime Start at</p>
                                                <p class="res-activity-time">
                                                    <i class="fa fa-clock-o"></i>
                                                    {{ $latestOvertime->start_time  }}
                                                </p>
                                            </li>
                                            @endif
                                            @if ($latestOvertime  && $latestOvertime->end_time !==null)
                                            <li>
                                                <p class="mb-0">Overtime Ended at</p>
                                                <p class="res-activity-time">
                                                    <i class="fa fa-clock-o"></i>
                                                    {{ $latestOvertime->end_time  }}
                                                </p>
                                            </li>
                                            @endif @if ($latest->timeOut)
                                                <li>
                                                    <p class="mb-0">Punch Out at</p>
                                                    <p class="res-activity-time">
                                                        <i class="fa fa-clock-o"></i>
                                                        {{ $latest->timeOut }}
                                                    </p>
                                                </li>
                                            @endif
                                        @endif
                        </ul>

                    </div>
                </div>
            </div>

        </div>

        <!-- CALENDAR RECORD -->

        <div class="row filter-row">
            <div class="calendar-container">
                <div class="calendar-header">
                    <div class="filter-controls">
                        <div class="row">
                            <!-- Cut-off Period Select -->
                            <div class="col-12 col-md-6 mb-3">
                                <div class="form-group form-focus select-focus">
                                    <select class="select floating" id="monthSelect">
                                        <option value="0">December - January 1st Cut-off</option>
                                        <option value="1">January 2nd Cut-off</option>
                                        <option value="2">January - February 1st Cut-off</option>
                                        <option value="3">February 2nd Cut-off</option>
                                        <option value="4">February - March 1st Cut-off</option>
                                        <option value="5">March 2nd Cut-off</option>
                                        <option value="6">March - April 1st Cut-off</option>
                                        <option value="7">April 2nd Cut-off</option>
                                        <option value="8">April - May 1st Cut-off</option>
                                        <option value="9">May 2nd Cut-off</option>
                                        <option value="10">May - June 1st Cut-off</option>
                                        <option value="11">June 2nd Cut-off</option>
                                        <option value="12">June - July 1st Cut-off</option>
                                        <option value="13">July 2nd Cut-off</option>
                                        <option value="14">July - August 1st Cut-off</option>
                                        <option value="15">August 2nd Cut-off</option>
                                        <option value="16">August - September 1st Cut-off</option>
                                        <option value="17">September 2nd Cut-off</option>
                                        <option value="18">September - October 1st Cut-off</option>
                                        <option value="19">October 2nd Cut-off</option>
                                        <option value="20">October - November 1st Cut-off</option>
                                        <option value="21">November 2nd Cut-off</option>
                                        <option value="22">November - December 1st Cut-off</option>
                                        <option value="23">December 2nd Cut-off</option>
                                    </select>
                                    <label class="focus-label">Cut-off Period</label>
                                </div>
                            </div>

                            <!-- Year Select -->
                            <div class="col-12 col-md-6 mb-3">
                                <div class="form-group form-focus select-focus">
                                    <select name="" id="yearSelect" class="select floating">
                                        <option value="2023">2023</option>
                                        <option value="2024">2024</option>
                                        <option value="2025">2025</option>
                                        <option value="2026">2026</option>
                                    </select>
                                    <label class="focus-label">Year</label>
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="row">
                            <div class="col-12 col-md-6 mb-3 text-center text-md-left">
                                <button id="searchButton" class="btn btn-danger">Search</button>
                            </div>
                            <div class="col-12 col-md-6 mb-3 text-center text-md-left">
                                <button id="saveButton" class="btn btn-outline-danger">Send</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="calendar"></div>
            </div>
        </div>
        <!-- /Calendar Record -->

        <!-- Announcement & Policies -->

        <div class="row">
            <div class="col-xl-6 col-md-12 d-flex">
                <div class="card employee-month-card flex-fill">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-lg-9 col-md-12">
                                <div class="employee-month-details">
                                    <h4>{{ $latestAnnouncement->annTitle ?? 'No Announcement is Displayed' }}</h4>
                                    <p>{{ $latestAnnouncement->annDescription ?? '' }}</p>
                                </div>
                                <div class="employee-month-content">
                                    <h6><strong>{{ $latestAnnouncement->poster->fName ?? '' }}</strong></h6>
                                    <p>{{ $latestAnnouncement->poster->position ?? '' }}</p>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-12">
                                <div class="employee-month-img">
                                    <img src="{{ asset('images/' . ($latestAnnouncement->annImage ?? 'default.png')) }}"
                                        class="img-fluid" alt="User">
                                    <!-- Added parentheses for clarity and default image -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

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
                                            class="fa-solid fa-chevron-right"></i></button></div>
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
                                                            <li>Updated on : {{ $policy->updated_at->format('d M Y') }}
                                                            </li>
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

    <!-- GOOGLE MAP API -->

    <!-- CLOCKED IN SCRIPT -->
    <script>
        let videoStream;

        document.getElementById("checkInButton").addEventListener("click", getLocation);
        let lowAccuracyCheckIn = false;
        let geofences = [];

        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition, showError, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0,
                });
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        }

        function showPosition(position) {
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;
            document.getElementById("latitude").value = latitude;
            document.getElementById("longitude").value = longitude;

            fetch("{{ url('emp/dashboard') }}", {
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        latitude,
                        longitude
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert(data.message);
                        window.location.reload();
                    } else if (data.status === 'low_accuracy') {
                        alert(data.message);
                        lowAccuracyCheckIn = true;
                        $('#imageUploadModal').modal('show');
                        startCamera();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Check-in error:', error);
                    alert("Error submitting check-in. Please try again.");
                });
        }

        // Start the camera feed
        function startCamera() {
            const video = document.getElementById('video');
            navigator.mediaDevices.getUserMedia({
                    video: true
                })
                .then(stream => {
                    videoStream = stream;
                    video.srcObject = stream;
                })
                .catch(error => console.error("Camera access error:", error));
        }

        // Capture the image from the video feed
        document.getElementById("captureButton").addEventListener("click", function() {
            const canvas = document.getElementById("canvas");
            const video = document.getElementById("video");
            const capturedImage = document.getElementById("capturedImage");

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
            capturedImage.src = canvas.toDataURL('image/png');
            capturedImage.style.display = "block";
            canvas.style.display = "none";
        });

        // Stop the camera feed
        function stopCamera() {
            if (videoStream) {
                videoStream.getTracks().forEach(track => track.stop());
            }
        }

        function showError(error) {
            console.error("Geolocation error:", error);
            switch (error.code) {
                case error.PERMISSION_DENIED:
                    if (confirm("Location access was denied. Would you like to try again?")) {
                        getLocation();
                    } else {
                        alert("Please enable location access in your browser settings.");
                    }
                    break;
                case error.POSITION_UNAVAILABLE:
                    alert("Location information is unavailable.");
                    break;
                case error.TIMEOUT:
                    alert("The request to get user location timed out. Please try again.");
                    break;
                case error.UNKNOWN_ERROR:
                    alert("An unknown error occurred.");
                    break;
            }
        }

        function getAddressFromLatLng(latitude, longitude) {
            const apiKey = 'AIzaSyCoZSVkyGR645u4B_OOFmepLzrRBB8Hgmc'; // Replace with your actual Google Maps API key
            const geocodeUrl =
                `https://maps.googleapis.com/maps/api/geocode/json?latlng=${latitude},${longitude}&key=${apiKey}`;

            fetch(geocodeUrl)
                .then(response => response.json())
                .then(data => {
                    console.log("Geocode response:", data);
                    if (data.status === "OK") {
                        const address = data.results[0].formatted_address;
                        document.getElementById("location").value = address;

                        // Submit the form
                        submitForm();
                    } else {
                        alert("Could not fetch address. Please try again.");
                    }
                })
                .catch(error => {
                    alert("Error fetching address. Please check your connection.");
                    console.error("Geocoding error:", error);
                });
        }

        function submitForm() {
            const formData = new FormData(document.getElementById("clockInForm"));

            fetch("{{ url('emp/dashboard') }}", {
                    method: "POST",
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    console.log("Full check-in response:", data);
                    if (data.status === 'low_accuracy') {
                        alert(data.message);
                        $('#imageUploadModal').modal('show');
                    } else if (data.status === 'success') {
                        alert(data.message);
                        window.location.reload();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Check-in error:', error);
                    alert("Error submitting check-in. Please try again.");
                });
        }

        // Submit captured image
        document.getElementById("submitImage").addEventListener("click", function() {
            const capturedImage = document.getElementById("capturedImage").src;

            // Validate if an image was captured
            if (!capturedImage || capturedImage === '') {
                alert("Please capture an image before submitting.");
                return;
            }

            // Convert base64 image to file for submission
            fetch(capturedImage)
                .then(res => res.blob())
                .then(blob => {
                    const formData = new FormData(document.getElementById("clockInForm"));
                    formData.append('image', blob, 'checkin_photo.png');
                    formData.append('low_accuracy', lowAccuracyCheckIn);

                    fetch("{{ url('emp/dashboard') }}", {
                            method: "POST",
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log("Image upload response:", data);
                            if (data.status === 'success') {
                                stopCamera();
                                $('#imageUploadModal').modal('hide');
                                alert('Check-in completed with photo!');
                                window.location.reload();
                            } else {
                                alert(data.message || "Error submitting clock-in. Please try again.");
                            }
                        })
                        .catch(error => {
                            console.error("Error uploading image:", error);
                            alert("Error submitting clock-in. Please try again.");
                        });
                });
        });

        // Stop the camera feed when modal is closed
        $('#imageUploadModal').on('hidden.bs.modal', stopCamera);
    </script>

    <!-- CLOCKED OUT SCRIPT -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("clockOutButton").addEventListener("click", getOutLocation);

            let lowAccuracyClockOut = false;
            let clockOutVideoStream;

            function getOutLocation() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(showOutPosition, showOutError, {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0,
                    });
                } else {
                    alert("Geolocation is not supported by this browser.");
                }
            }

            function showOutPosition(position) {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;
                document.getElementById("outLatitude").value = latitude;
                document.getElementById("outLongitude").value = longitude;

                // Fetch address from latitude and longitude
                getOutAddressFromLatLng(latitude, longitude);
            }

            function showOutError(error) {
                console.error("Geolocation error:", error);
                switch (error.code) {
                    case error.PERMISSION_DENIED:
                        alert("Location permission was denied. Please enable location access and try again.");
                        break;
                    case error.POSITION_UNAVAILABLE:
                        alert("Location information is unavailable. Please check your device settings.");
                        break;
                    case error.TIMEOUT:
                        alert("The request to get your location timed out. Please try again.");
                        break;
                    default:
                        alert("An unknown error occurred. Please try again.");
                }
            }

            function getOutAddressFromLatLng(latitude, longitude) {
                const apiKey = 'AIzaSyCoZSVkyGR645u4B_OOFmepLzrRBB8Hgmc';
                const geocodeUrl =
                    `https://maps.googleapis.com/maps/api/geocode/json?latlng=${latitude},${longitude}&key=${apiKey}`;

                fetch(geocodeUrl)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.status === "OK") {
                            const address = data.results[0].formatted_address;
                            document.getElementById("outLocation").value = address;
                            submitClockOutForm();
                        } else {
                            throw new Error(`Geocoding failed: ${data.status}`);
                        }
                    })
                    .catch(error => {
                        alert(`Error fetching address: ${error.message}`);
                        console.error("Geocoding error:", error);
                    });
            }

            function submitClockOutForm() {
                const formData = new FormData(document.getElementById("clockOutForm"));
                formData.append('low_accuracy', lowAccuracyClockOut); // Ensure the flag is sent

                fetch("{{ url('emp/dashboard/clockout') }}", {
                        method: "POST",
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => {
                        console.log("Raw response:", response);
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log("Clock-Out response received from backend:", data);
                        if (data.status === 'success') {
                            alert(data.message);
                            window.location.reload();
                        } else if (data.status === 'low_accuracy') {
                            lowAccuracyClockOut = true; // Set this to true for subsequent requests
                            console.log("Low accuracy response detected.");

                            // Close exampleModal before showing clockOutImageModal
                            $('#exampleModal').modal('hide');

                            setTimeout(() => {
                                $('#clockOutImageModal').modal('show');
                                startOutCamera();
                            }, 500);
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => {
                        console.error("Clock-out submission error:", error);
                        alert("Error submitting clock-out. Please try again.");
                    });
            }


            function startOutCamera() {
                console.log("Initializing camera...");
                const video = document.getElementById('outVideo');
                navigator.mediaDevices.getUserMedia({
                        video: true
                    })
                    .then(stream => {
                        clockOutVideoStream = stream;
                        video.srcObject = stream;
                        console.log("Camera started successfully.");
                    })
                    .catch(error => {
                        console.error("Error accessing camera:", error);
                        alert("Unable to access the camera. Please check permissions.");
                    });
            }

            document.getElementById("outCaptureButton").addEventListener("click", function() {
                const canvas = document.getElementById("outCanvas");
                const video = document.getElementById("outVideo");
                const capturedImage = document.getElementById("outCapturedImage");

                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                canvas.getContext('2d').drawImage(video, 0, 0);
                capturedImage.src = canvas.toDataURL('image/png');
                capturedImage.style.display = "block";
            });

            function stopOutCamera() {
                if (clockOutVideoStream) {
                    clockOutVideoStream.getTracks().forEach(track => track.stop());
                }
            }

            document.getElementById("outSubmitImage").addEventListener("click", function() {
                const capturedImage = document.getElementById("outCapturedImage").src;

                if (!capturedImage || capturedImage === '') {
                    alert("Please capture an image before submitting.");
                    return;
                }

                fetch(capturedImage)
                    .then(res => res.blob())
                    .then(blob => {
                        const formData = new FormData();
                        formData.append('image', blob, 'clockout_photo.png'); // Attach the image
                        formData.append('latitude', document.getElementById("outLatitude").value);
                        formData.append('longitude', document.getElementById("outLongitude").value);
                        formData.append('location', document.getElementById("outLocation").value);
                        formData.append('low_accuracy', true); // Explicitly set the low_accuracy flag

                        console.log("Submitting form data:", [...formData
                            .entries()
                        ]); // Log all form data

                        fetch("{{ url('emp/dashboard/clockout') }}", {
                                method: "POST",
                                body: formData,
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]').content
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.status === 'success') {
                                    stopOutCamera();
                                    $('#clockOutImageModal').modal('hide');
                                    alert('Clock-out completed with photo!');
                                    window.location.reload();
                                } else {
                                    alert(data.message ||
                                        "Error submitting clock-out. Please try again.");
                                }
                            })
                            .catch(error => {
                                console.error("Error uploading image:", error);
                                alert("Error submitting clock-out. Please try again.");
                            });
                    });
            });

            $('#clockOutImageModal').on('hidden.bs.modal', function() {
                stopOutCamera();
            });
        });
    </script>

    <!-- Overtime IN Script -->
    <script>
        // Listen for the Overtime-In button click
        document.getElementById("overtimeInButton").addEventListener("click", function() {
            // Confirm with the user before proceeding
            if (confirm("Are you sure you want to start overtime?")) {
                // Get the overtime form
                const form = document.getElementById("overtimeInForm");

                // Submit the form
                form.submit();
            }
        });
    </script>

    <!-- Overtime Out Script -->
    <script>
        // Listen for the Overtime-Out button click
        document.getElementById("overtimeOutButton").addEventListener("click", function() {
            // Confirm with the user before proceeding
            if (confirm("Are you sure you want to end overtime?")) {
                // Get the overtime form
                const form = document.getElementById("overtimeOutForm");

                // Add a hidden _method input to simulate PUT
                const methodInput = document.createElement("input");
                methodInput.type = "hidden";
                methodInput.name = "_method";
                methodInput.value = "PUT";
                form.appendChild(methodInput);

                // Submit the form
                form.submit();
            }
        });
    </script>

    <!-- CAROUSEL -->
    <script>
        $(document).ready(function() {
            // Initialize the Owl Carousel
            var $carousel = $('.company-slider').owlCarousel({
                items: 2, // Number of items to show
                loop: true, // Enable looping
                margin: 20, // Margin between items
                nav: false, // Disable default navigation
                dots: false // Disable dots navigation
            });

            // Bind click events to your custom navigation buttons
            $('.owl-prev').on('click', function() {
                $carousel.trigger('prev.owl.carousel');
            });
            $('.owl-next').on('click', function() {
                $carousel.trigger('next.owl.carousel');
            });
        });
    </script>

    <!-- POLICY DOWNLOAD -->
    <script>
        function downloadPolicy(url) {
            if (url) {
                window.open(url, '_blank');
            } else {
                alert('No document available for download');
            }
        }
    </script>

    <!-- TIME -->
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

    <!-- CALENDAR -->

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendar = document.getElementById('calendar');
            const monthSelect = document.getElementById('monthSelect');
            const searchButton = document.getElementById('searchButton');
            const saveButton = document.getElementById('saveButton');
            const yearSelect = document.getElementById('yearSelect');
            const daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'June', 'Jul', 'Aug', 'Sept', 'Oct', 'Nov',
                'Dec'
            ];

            function fetchHolidays(callback) {
                $.ajax({
                    url: '/emp/dashboard/holidays/',
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        console.log("Fetched holidays: ", data); // Debugging line
                        callback(data);
                    },
                    error: function(err) {
                        console.error('Error fetching holidays:', err);
                    }
                });
            }

            function renderCalendar(startDate, endDate, attendanceData, leaveData, holidays, overtimeData, status =
                'new') {
                console.log("Rendering calendar from ", startDate, " to ", endDate);

                calendar.innerHTML = '';

                let totalWorkedSeconds = 0;
                let totalLateSeconds = 0;
                let totalOvertimeSeconds = 0; // Counter for total overtime
                let paidLeaveCount = 0; // Counter for paid leaves
                let unpaidLeaveCount = 0; // Counter for unpaid leaves
                let totalRegularHolidaySeconds = 0;
                let totalSpecialHolidaySeconds = 0;

                let currentDate = new Date(startDate);
                while (currentDate <= endDate) {
                    const dayDiv = document.createElement('div');
                    dayDiv.className = 'day-att';
                    dayDiv.style.padding = '5px';

                    const dateHeader = document.createElement('div');
                    dateHeader.className = 'day-header-att';
                    dateHeader.innerText =
                        `${monthNames[currentDate.getMonth()]} ${currentDate.getDate()}, ${currentDate.getFullYear()}, ${daysOfWeek[currentDate.getDay()]}`;
                    dayDiv.appendChild(dateHeader);

                    // Check for attendance on this day
                    const dayData = attendanceData.find(item => {
                        const itemDate = new Date(item.date);
                        return itemDate.getDate() === currentDate.getDate() &&
                            itemDate.getMonth() === currentDate.getMonth() &&
                            itemDate.getFullYear() === currentDate.getFullYear();
                    });

                    if (dayData) {
                        const timeIn = document.createElement('div');
                        timeIn.className = 'calendar-text';
                        timeIn.innerText = "Time In: " + dayData.timeIn;
                        dayDiv.appendChild(timeIn);

                        const timeOut = document.createElement('div');
                        timeOut.className = 'calendar-text';
                        timeOut.innerText = "Time Out: " + (dayData.timeOut || 'N/A');
                        dayDiv.appendChild(timeOut);

                        const timeTotal = document.createElement('div');
                        timeTotal.className = 'calendar-text';
                        timeTotal.innerText = "Total: " + (dayData.timeTotal || 'N/A');
                        dayDiv.appendChild(timeTotal);

                        const totalLate = document.createElement('div');
                        totalLate.className = 'calendar-text';
                        totalLate.innerText = "Total Late: " + (dayData.totalLate || 'N/A');
                        dayDiv.appendChild(totalLate);

                        if (dayData.totalLate) {
                            const lateParts = dayData.totalLate.split(':').map(Number);
                            totalLateSeconds += lateParts[0] * 3600 + lateParts[1] * 60 + lateParts[2];
                        }

                        if (dayData.timeTotal) {
                            const timeParts = dayData.timeTotal.split(':').map(Number);
                            const workedSeconds = timeParts[0] * 3600 + timeParts[1] * 60 + timeParts[2];
                            totalWorkedSeconds += workedSeconds;

                            // Allocate holiday hours if applicable
                            const holiday = checkHoliday(currentDate, holidays);
                            if (holiday) {
                                if (holiday.type === 'Regular') {
                                    totalRegularHolidaySeconds += workedSeconds;
                                } else if (holiday.type === 'Special') {
                                    totalSpecialHolidaySeconds += workedSeconds;
                                }
                            }
                        }

                    }

                    // Check for approved leave on this day
                    const leaveOnDay = leaveData.filter(leave => {
                        const leaveStart = new Date(leave.start_date);
                        const leaveEnd = new Date(leave.end_date);
                        return leave.status === 'Approved' && currentDate >= leaveStart && currentDate <=
                            leaveEnd;
                    });

                    leaveOnDay.forEach(leave => {
                        const leaveTypeDiv = document.createElement('div');
                        leaveTypeDiv.className = 'calendar-text leave-type-button';
                        leaveTypeDiv.innerText = leave.leave_type.leaveType;

                        if (leave.leave_type.is_paid) {
                            leaveTypeDiv.style.backgroundColor = 'green';
                            if (leave.is_half_day) { // Check if the leave is half-day
                                paidLeaveCount += 0.5; // Increment by 0.5 for half-day
                            } else {
                                paidLeaveCount++; // Increment by 1 for full-day
                            }
                        } else {
                            leaveTypeDiv.style.backgroundColor = 'red';
                            if (leave.is_half_day) { // Check if the leave is half-day
                                unpaidLeaveCount += 0.5; // Increment by 0.5 for half-day unpaid leave
                            } else {
                                unpaidLeaveCount++; // Increment by 1 for full-day unpaid leave
                            }
                        }

                        leaveTypeDiv.style.color = 'white';
                        leaveTypeDiv.style.padding = '5px';
                        leaveTypeDiv.style.borderRadius = '5px';
                        leaveTypeDiv.style.marginTop = '5px';
                        dayDiv.appendChild(leaveTypeDiv);
                    });

                    // Check for approved overtime on this day
                    const overtimeOnDay = overtimeData.find(ot => {
                        const otDate = new Date(ot.date);
                        return otDate.getDate() === currentDate.getDate() &&
                            otDate.getMonth() === currentDate.getMonth() &&
                            otDate.getFullYear() === currentDate.getFullYear();
                    });

                    if (overtimeOnDay) {
                        const overtimeDiv = document.createElement('div');
                        overtimeDiv.className = 'calendar-text overtime-approved';
                        overtimeDiv.innerText = "Approved Overtime: " + overtimeOnDay.total_hours;
                        dayDiv.appendChild(overtimeDiv);

                        const otParts = overtimeOnDay.total_hours.split(':').map(Number);
                        totalOvertimeSeconds += otParts[0] * 3600 + otParts[1] * 60 + otParts[2];
                    }

                    // Check for holidays
                    const holiday = checkHoliday(currentDate, holidays);
                    if (holiday) {
                        const holidayDiv = document.createElement('div');
                        holidayDiv.className = 'holiday-button';
                        holidayDiv.innerText = holiday.title;

                        if (holiday.type === 'Regular') {
                            holidayDiv.style.backgroundColor = 'red';
                        } else if (holiday.type === 'Special') {
                            holidayDiv.style.backgroundColor = 'green';
                        }

                        holidayDiv.style.color = 'white';
                        holidayDiv.style.padding = '5px';
                        holidayDiv.style.marginTop = '5px';
                        holidayDiv.style.borderRadius = '5px';
                        dayDiv.appendChild(holidayDiv);
                    }

                    calendar.appendChild(dayDiv);
                    currentDate.setDate(currentDate.getDate() + 1);
                }

                const workedHours = Math.floor(totalWorkedSeconds / 3600);
                const workedMinutes = Math.floor((totalWorkedSeconds % 3600) / 60);
                const workedSeconds = totalWorkedSeconds % 60;
                const totalWorkedFormatted =
                    `${workedHours.toString().padStart(2, '0')}:${workedMinutes.toString().padStart(2, '0')}:${workedSeconds.toString().padStart(2, '0')}`;

                const overtimeHours = Math.floor(totalOvertimeSeconds / 3600);
                const overtimeMinutes = Math.floor((totalOvertimeSeconds % 3600) / 60);
                const overtimeSeconds = totalOvertimeSeconds % 60;
                const totalOvertimeFormatted =
                    `${overtimeHours.toString().padStart(2, '0')}:${overtimeMinutes.toString().padStart(2, '0')}:${overtimeSeconds.toString().padStart(2, '0')}`;

                const lateHours = Math.floor(totalLateSeconds / 3600);
                const lateMinutes = Math.floor((totalLateSeconds % 3600) / 60);
                const lateSeconds = totalLateSeconds % 60;
                const totalLateFormatted =
                    `${lateHours.toString().padStart(2, '0')}:${lateMinutes.toString().padStart(2, '0')}:${lateSeconds.toString().padStart(2, '0')}`;

                // Display total stats at the bottom
                const totalBox = document.createElement('div');
                totalBox.className = 'day total-box';
                totalBox.innerText = `
            Total Worked Hours: ${formatTime(totalWorkedSeconds)}
            Total Late: ${formatTime(totalLateSeconds)}
            Approved Overtime: ${formatTime(totalOvertimeSeconds)}
            Regular Holiday Hours: ${formatTime(totalRegularHolidaySeconds)}
            Special Holiday Hours: ${formatTime(totalSpecialHolidaySeconds)}
            Paid Leaves: ${paidLeaveCount}
            Unpaid Leaves: ${unpaidLeaveCount}
            Status: ${status || 'new'}
        `;
                calendar.appendChild(totalBox);
            }


            function checkHoliday(date, holidays) {
                const formattedDate =
                    `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, '0')}-${date.getDate().toString().padStart(2, '0')}`;
                console.log("Checking holiday for: ", formattedDate); // Debugging line
                const holiday = holidays.find(h => h.holidayDate === formattedDate);
                return holiday;
            }

            function fetchAttendanceData(startDate, endDate, callback) {
                $.ajax({
                    url: "{{ route('attendance.get') }}",
                    method: 'GET',
                    dataType: 'json',
                    data: {
                        start_date: startDate.toISOString().split('T')[0],
                        end_date: endDate.toISOString().split('T')[0]
                    },
                    success: function(data) {
                        callback(data.attendance, data.leaves, data
                            .overtime); // Now includes overtime data
                    },
                    error: function(err) {
                        console.error('Error fetching attendance data:', err);
                    }
                });
            }

            function fetchStatus(cutoff, callback) {
                $.ajax({
                    url: "{{ route('attendance.status') }}", // Ensure this route matches your Laravel route for fetching status
                    method: 'GET',
                    dataType: 'json',
                    data: {
                        cutoff: cutoff
                    },
                    success: function(data) {
                        callback(data.status);
                    },
                    error: function(err) {
                        console.error('Error fetching status:', err);
                        callback('new'); // Default to 'new' if there's an error
                        // Handle the error on the front-end, e.g., display an alert or message to the user
                    }
                });
            }

            function getCutoffDates(monthIndex, year) {
                let startDate, endDate;

                switch (monthIndex) {
                    case 0: // December 21 to January 5
                        startDate = new Date(Date.UTC(year - 1, 11, 21));
                        endDate = new Date(Date.UTC(year, 0, 5));
                        break;
                    case 1: // January 6 to January 20
                        startDate = new Date(Date.UTC(year, 0, 6));
                        endDate = new Date(Date.UTC(year, 0, 20));
                        break;
                    case 2: // January 21 to February 5
                        startDate = new Date(Date.UTC(year, 0, 21));
                        endDate = new Date(Date.UTC(year, 1, 5));
                        break;
                    case 3: // February 6 to February 20
                        startDate = new Date(Date.UTC(year, 1, 6));
                        endDate = new Date(Date.UTC(year, 1, 20));
                        break;
                    case 4: // February 21 to March 5
                        startDate = new Date(Date.UTC(year, 1, 21));
                        endDate = new Date(Date.UTC(year, 2, 5));
                        break;
                    case 5: // March 6 to March 20
                        startDate = new Date(Date.UTC(year, 2, 6));
                        endDate = new Date(Date.UTC(year, 2, 20));
                        break;
                    case 6: // March 21 to April 5
                        startDate = new Date(Date.UTC(year, 2, 21));
                        endDate = new Date(Date.UTC(year, 3, 5));
                        break;
                    case 7: // April 6 to April 20
                        startDate = new Date(Date.UTC(year, 3, 6));
                        endDate = new Date(Date.UTC(year, 3, 20));
                        break;
                    case 8: // April 21 to May 5
                        startDate = new Date(Date.UTC(year, 3, 21));
                        endDate = new Date(Date.UTC(year, 4, 5));
                        break;
                    case 9: // May 6 to May 20
                        startDate = new Date(Date.UTC(year, 4, 6));
                        endDate = new Date(Date.UTC(year, 4, 20));
                        break;
                    case 10: // May 21 to June 5
                        startDate = new Date(Date.UTC(year, 4, 21));
                        endDate = new Date(Date.UTC(year, 5, 5));
                        break;
                    case 11: // June 6 to June 20
                        startDate = new Date(Date.UTC(year, 5, 6));
                        endDate = new Date(Date.UTC(year, 5, 20));
                        break;
                    case 12: // June 21 to July 5
                        startDate = new Date(Date.UTC(year, 5, 21));
                        endDate = new Date(Date.UTC(year, 6, 5));
                        break;
                    case 13: // July 6 to July 20
                        startDate = new Date(Date.UTC(year, 6, 6));
                        endDate = new Date(Date.UTC(year, 6, 20));
                        break;
                    case 14: // July 21 to August 5
                        startDate = new Date(Date.UTC(year, 6, 21));
                        endDate = new Date(Date.UTC(year, 7, 5));
                        break;
                    case 15: // August 6 to August 20
                        startDate = new Date(Date.UTC(year, 7, 6));
                        endDate = new Date(Date.UTC(year, 7, 20));
                        break;
                    case 16: // August 21 to September 5
                        startDate = new Date(Date.UTC(year, 7, 21));
                        endDate = new Date(Date.UTC(year, 8, 5));
                        break;
                    case 17: // September 6 to September 20
                        startDate = new Date(Date.UTC(year, 8, 6));
                        endDate = new Date(Date.UTC(year, 8, 20));
                        break;
                    case 18: // September 21 to October 5
                        startDate = new Date(Date.UTC(year, 8, 21));
                        endDate = new Date(Date.UTC(year, 9, 5));
                        break;
                    case 19: // October 6 to October 20
                        startDate = new Date(Date.UTC(year, 9, 6));
                        endDate = new Date(Date.UTC(year, 9, 20));
                        break;
                    case 20: // October 21 to November 5
                        startDate = new Date(Date.UTC(year, 9, 21));
                        endDate = new Date(Date.UTC(year, 10, 5));
                        break;
                    case 21: // November 6 to November 20
                        startDate = new Date(Date.UTC(year, 10, 6));
                        endDate = new Date(Date.UTC(year, 10, 20));
                        break;
                    case 22: // November 21 to December 5
                        startDate = new Date(Date.UTC(year, 10, 21));
                        endDate = new Date(Date.UTC(year, 11, 5));
                        break;
                    case 23: // December 6 to December 20
                        startDate = new Date(Date.UTC(year, 11, 6));
                        endDate = new Date(Date.UTC(year, 11, 20));
                        break;
                }

                return {
                    startDate,
                    endDate
                };
            }


            function searchCalendar() {
                const monthIndex = parseInt(monthSelect.value);
                const year = parseInt(yearSelect.value);
                const {
                    startDate,
                    endDate
                } = getCutoffDates(monthIndex, year);

                fetchHolidays(function(holidays) {
                    fetchAttendanceData(startDate, endDate, function(attendanceData, leaveData,
                        overtimeData) {
                        renderCalendar(startDate, endDate, attendanceData, leaveData, holidays,
                            overtimeData);
                    });
                });
            }


            function saveAttendance() {
                const monthSelect = document.getElementById('monthSelect');
                const yearSelect = document.getElementById('yearSelect');
                const selectedOptionText = monthSelect.options[monthSelect.selectedIndex].text;
                const year = parseInt(yearSelect.value);
                const cutoff = `${selectedOptionText}`;

                const {
                    startDate,
                    endDate
                } = getCutoffDates(monthSelect.selectedIndex, year);

                const totalBox = document.querySelector('.total-box');

                // Extract stats from the Total Box
                const totalWorked = totalBox.innerText.match(/Total Worked Hours: (\d+:\d{2}:\d{2})/)[1];
                const totalLate = totalBox.innerText.match(/Total Late: (\d{2}:\d{2}:\d{2})/)[1];
                const unpaidLeaveCount = totalBox.innerText.match(/Unpaid Leaves: (\d+)/)[1];
                const paidLeaveCount = parseFloat(totalBox.innerText.match(/Paid Leaves: ([\d.]+)/)[1]);
                const approvedOvertime = totalBox.innerText.match(/Approved Overtime: (\d{2}:\d{2}:\d{2})/)[1];
                const regularHolidayHours = totalBox.innerText.match(/Regular Holiday Hours: (\d{2}:\d{2}:\d{2})/)[
                    1];
                const specialHolidayHours = totalBox.innerText.match(/Special Holiday Hours: (\d{2}:\d{2}:\d{2})/)[
                    1];

                const data = {
                    total_worked: totalWorked,
                    total_late: totalLate,
                    cutoff: cutoff,
                    start_date: startDate.toISOString().split('T')[0],
                    end_date: endDate.toISOString().split('T')[0],
                    unpaid_leave: unpaidLeaveCount,
                    paid_leave: paidLeaveCount,
                    approved_overtime: approvedOvertime,
                    regular_holiday_hours: regularHolidayHours,
                    special_holiday_hours: specialHolidayHours,
                    year: year,
                    status: 'pending'
                };

                console.log('Data to be saved:', data);

                // Check if the attendance record already exists
                $.ajax({
                    url: "{{ route('attendance.check') }}",
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: data,
                    success: function(response) {
                        console.log('Check response:', response);
                        if (response.exists) {
                            if (response.status === 'pending') {
                                alert(
                                    'The attendance sheet is already recorded and is waiting for approval.'
                                );
                                updateStatus('pending');
                            } else if (response.status === 'approved' || response.status ===
                                'rejected') {
                                alert(`The attendance sheet has already been ${response.status}.`);
                                updateStatus(response.status);
                            } else {
                                alert('Attendance record already exists for this period.');
                            }
                        } else {
                            // Ask for confirmation before saving
                            if (confirm('Do you wish to send this cut-off?')) {
                                // Save the attendance record
                                $.ajax({
                                    url: "{{ route('attendance.save') }}",
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                            'content')
                                    },
                                    data: data,
                                    success: function(response) {
                                        console.log('Attendance saved successfully:',
                                            response);
                                        alert('Attendance saved successfully.');
                                        updateStatus('pending');
                                    },
                                    error: function(err) {
                                        console.error('Error saving attendance:', err);
                                        if (err.status === 409) {
                                            alert(err.responseJSON
                                                .message
                                            ); // Display specific conflict message
                                        } else {
                                            alert('Error saving attendance.');
                                        }
                                    }
                                });
                            }
                        }
                    },
                    error: function(err) {
                        console.error('Error checking attendance:', err);
                        alert('Error checking attendance.');
                    }
                });
            }


            function updateStatus(status) {
                const totalBox = document.querySelector('.total-box');
                if (totalBox) {
                    const newText = totalBox.innerText.replace(/Status:.*/, `Status: ${status}`);
                    totalBox.innerText = newText;
                    console.log('Status updated to:', status);
                }
            }

            searchButton.addEventListener('click', searchCalendar);
            saveButton.addEventListener('click',
                saveAttendance);

            // Initial search when the page loads
            const currentDate = new Date();
            const currentDay = currentDate.getDate();
            let initialMonthIndex;

            if (currentDay <= 10) {
                initialMonthIndex = currentDate.getMonth() * 2;
            } else if (currentDay <= 24) {
                initialMonthIndex = currentDate.getMonth() * 2 + 1;
            } else {
                initialMonthIndex = currentDate.getMonth() * 2 + 2;
            }

            monthSelect.value = initialMonthIndex.toString();
            yearSelect.value = currentDate.getFullYear().toString();

            searchCalendar();
        });
    </script>

    <!-- TIMER COUNTDOWN -->
    <script>
        const countdownElement = document.getElementById('countdown');
        const countdownKey = 'countdownEndTime';
        const countdownTypeKey = 'countdownType';

        function formatTime(seconds) {
            const hours = Math.floor(seconds / 3600);
            const minutes = Math.floor((seconds % 3600) / 60);
            const secs = seconds % 60;
            return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
        }

        function updateCountdown() {
            const endTime = localStorage.getItem(countdownKey);
            if (endTime) {
                const now = new Date().getTime();
                const timeRemaining = Math.max(Math.floor((endTime - now) / 1000), 0);

                countdownElement.textContent = formatTime(timeRemaining);

                if (timeRemaining > 0) {
                    setTimeout(updateCountdown, 1000);
                } else {
                    countdownElement.textContent = "Time's up!";
                    localStorage.removeItem(countdownKey);
                    localStorage.removeItem(countdownTypeKey);
                }
            }
        }

        function startCountdown(durationMinutes) {
            const now = new Date().getTime();
            const durationMillis = durationMinutes * 60 * 1000;
            const endTime = now + durationMillis;

            localStorage.setItem(countdownKey, endTime);
            localStorage.setItem(countdownTypeKey, `${durationMinutes}min`);

            updateCountdown();
        }

        function resetCountdown() {
            localStorage.removeItem(countdownKey);
            localStorage.removeItem(countdownTypeKey);
            countdownElement.textContent = "00:00:00";
        }

        // Event listeners for break buttons
        document.getElementById('start1HourBreak').addEventListener('click', function(e) {
            e.preventDefault(); // Prevent form submission for countdown purposes
            startCountdown(60); // Start 1-hour countdown
            this.closest('form').submit(); // Submit the form to the server
        });

        document.getElementById('end1HourBreak').addEventListener('click', function(e) {
            e.preventDefault(); // Prevent form submission for countdown purposes
            if (localStorage.getItem(countdownTypeKey) === '60min') {
                resetCountdown(); // Reset only if 1-hour break is active
            }
            this.closest('form').submit(); // Submit the form to the server
        });

        document.getElementById('start15MinBreak').addEventListener('click', function(e) {
            e.preventDefault(); // Prevent form submission for countdown purposes
            startCountdown(15); // Start 15-minute countdown
            this.closest('form').submit(); // Submit the form to the server
        });

        document.getElementById('end15MinBreak').addEventListener('click', function(e) {
            e.preventDefault(); // Prevent form submission for countdown purposes
            if (localStorage.getItem(countdownTypeKey) === '15min') {
                resetCountdown(); // Reset only if 15-minute break is active
            }
            this.closest('form').submit(); // Submit the form to the server
        });

        // Initialize the countdown if it's already set
        updateCountdown();
    </script>

    <!-- PAGE REFRESH -->
    <script>
        // Function to refresh the page
        function refreshPage() {
            window.location.reload();
        }

        // Set timeout to refresh the page every 30 minutes (1800000 milliseconds)
        setTimeout(refreshPage, 1800000); // 30 minutes = 1800000 milliseconds
    </script>


@endsection
