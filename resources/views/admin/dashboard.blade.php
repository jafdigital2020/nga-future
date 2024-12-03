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
                                            <h4 class="text-primary" data-toggle="modal"
                                                data-target="#usersLoggedInToday" style="cursor:pointer">
                                                {{ $todayLoginCount }}
                                            </h4>
                                            <p>Present Today</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="attendance-details">
                                            <h4 class="text-pink" data-toggle="modal" data-target="#notClockedInModal"
                                                style="cursor: pointer;">
                                                {{ $notClockedInCount }}
                                            </h4>
                                            <p>No Clock-in Today</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="attendance-details">
                                            <h4 class="text-danger" data-toggle="modal" data-target="#lateUsersModal"
                                                style="cursor: pointer;">
                                                {{ $totalLateToday }}
                                            </h4>
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


                    <!-- MODAL FOR NO CLOCK IN -->
                    <div class="modal fade" id="notClockedInModal" tabindex="-1"
                        aria-labelledby="notClockedInModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="notClockedInModalLabel">Users Not Clocked In Today</h5>
                                    <button type="button" class="btn-close" data-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <ul class="list-group">
                                        @forelse ($notClockedInUsers as $user)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            {{ $user->fName  }} {{ $user->lName  }}
                                            <span
                                                class="badge bg-danger">{{ $user->department ?? 'No Department' }}</span>
                                        </li>
                                        @empty
                                        <li class="list-group-item text-center">No users found.</li>
                                        @endforelse
                                    </ul>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Late Users Modal -->
                    <div class="modal fade" id="lateUsersModal" tabindex="-1" aria-labelledby="lateUsersModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="lateUsersModalLabel">Users Marked as Late Today</h5>
                                    <button type="button" class="btn-close" data-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <ul class="list-group">
                                        @forelse ($lateUserDetails as $user)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            {{ $user->fName }} {{ $user->lName }}
                                            <span
                                                class="badge bg-warning">{{ $user->department ?? 'No Department' }}</span>
                                        </li>
                                        @empty
                                        <li class="list-group-item text-center">No late users found today.</li>
                                        @endforelse
                                    </ul>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- MODAL FOR CLOCK IN -->
                    <div class="modal fade" id="usersLoggedInToday" tabindex="-1" aria-labelledby="usersLoggedInToday"
                        aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="usersLoggedInToday">Users Clocked In Today</h5>
                                    <button type="button" class="btn-close" data-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <ul class="list-group">
                                        @forelse ($usersLoggedInToday as $user)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            {{ $user->fName  }} {{ $user->lName  }}
                                            <span
                                                class="badge bg-danger">{{ $user->department ?? 'No Department' }}</span>
                                        </li>
                                        @empty
                                        <li class="list-group-item text-center">No users found.</li>
                                        @endforelse
                                    </ul>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                </div>
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
        <div class="col-md-12 col-lg-6 col-xl-4 d-flex">
            <div class="card flex-fill">
                <div class="card-body">
                    <h4 class="card-title">
                        Leave Requests
                        <span class="badge bg-inverse-danger ml-2">{{ $leavePending->count() }}</span>
                    </h4>

                    <!-- Loop through each leave request -->
                    @foreach($leavePending->take(2) as $leave)
                    <div class="leave-info-box">
                        <div class="media align-items-center">
                            <a href="{{ isset($leave->user) ? url('profile/' . $leave->user->id) : '#' }}"
                                class="avatar">
                                <img alt="{{ $leave->user->name ?? 'Unknown' }}"
                                    src="{{ isset($leave->user->image) ? asset('images/' . $leave->user->image) : asset('images/default.png') }}" />
                            </a>
                            <div class="media-body">
                                <div class="text-sm my-0">{{ $leave->user->name ?? 'Unknown User' }}</div>
                            </div>
                        </div>
                        <div class="row align-items-center mt-3">
                            <div class="col-6">
                                <h6 class="mb-0">
                                    {{ $leave->start_date ? \Carbon\Carbon::parse($leave->start_date)->format('d M Y') : 'N/A' }}
                                    -
                                    {{ $leave->end_date ? \Carbon\Carbon::parse($leave->end_date)->format('d M Y') : 'N/A' }}
                                </h6>
                                <span class="text-sm text-muted">{{ $leave->leaveType->leaveType ?? 'N/A' }}</span>
                            </div>
                            <div class="col-6 text-right">
                                <div class="dropdown action-label">
                                    <a class="btn btn-white btn-sm btn-rounded dropdown-toggle" href="#"
                                        data-toggle="dropdown" aria-expanded="false">
                                        @if($leave->status == 'New')
                                        <i class="fa fa-dot-circle-o text-purple"></i> New
                                        @elseif($leave->status == 'Pending')
                                        <i class="fa fa-dot-circle-o text-info"></i> Pending
                                        @elseif($leave->status == 'Approved')
                                        <i class="fa fa-dot-circle-o text-success"></i> Approved
                                        @elseif($leave->status == 'Declined')
                                        <i class="fa fa-dot-circle-o text-danger"></i> Declined
                                        @else
                                        <i class="fa fa-dot-circle-o"></i> Unknown
                                        @endif
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="#"><i class="fa fa-dot-circle-o text-info"></i>
                                            Pending</a>

                                        <form id="approve-form-{{ $leave->id }}"
                                            action="{{ route('leave.approveadmin', $leave->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            <button type="button" class="dropdown-item approve-button"
                                                data-leave-id="{{ $leave->id }}">
                                                <i class="fa fa-dot-circle-o text-success"></i> Approved
                                            </button>
                                        </form>
                                        <form id="decline-form-{{ $leave->id }}"
                                            action="{{ route('leave.declineadmin', $leave->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            <button type="button" class="dropdown-item decline-button"
                                                data-leave-id="{{ $leave->id }}">
                                                <i class="fa fa-dot-circle-o text-danger"></i> Declined
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach


                    <!-- Load More Button (Optional) -->
                    @if($leavePending->count() > 2)
                    <div class="load-more text-center">
                        <a class="text-dark" href="{{ url('admin/leave') }}">Load More</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Loader for all leave requests -->
    <div id="global-loader" class="loader" style="display: none;">
        <div class="loading-bar-background">
            <div class="loading-bar">
                <div class="white-bars-container">
                    <div class="white-bar"></div>
                    <div class="white-bar"></div>
                    <div class="white-bar"></div>
                </div>
            </div>
        </div>
        <span class="loading-text">Processing...</span>
    </div>


    <!-- Statistics -->

    <div class="row">
        <div class="col-md-6 d-flex">
            <div class="card card-table flex-fill">
                <div class="card-header">
                    <h3 class="card-title mb-0">Overtime Requests <span
                            class="badge bg-inverse-danger ml-2">{{ $overtimeRequest->count() }}</span></h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table custom-table mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Date</th>
                                    <th>Total Hours</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($overtimeRequest->take(5) as $ot)
                                <tr>
                                    <td>
                                        <h2 class="table-avatar">
                                            <a href="#" class="avatar">
                                                @if ($ot->user->image)
                                                <img src="{{ asset('images/' . $ot->user->image) }}"
                                                    alt="Profile Image" />
                                                @else
                                                <img src="{{
                                                        asset('images/default.png')
                                                    }}" alt="Profile Image" />
                                                @endif</a>
                                            <a href="{{ url('admin/employee/edit/'.$ot->user->id) }}">{{ $ot->user->fName }}
                                                {{ $ot->user->lName }}
                                                <span>{{ $ot->user->position }}</span></a>
                                        </h2>
                                    </td>
                                    <td>{{ $ot->date }}</td>
                                    <td>{{ $ot->total_hours }}</td>
                                    <td class="text-center">
                                        <div class="dropdown action-label">
                                            <a class="btn btn-white btn-sm btn-rounded dropdown-toggle" href="#"
                                                data-toggle="dropdown" aria-expanded="false">
                                                @if($ot->status == 'New')
                                                <i class="fa fa-dot-circle-o text-purple"></i> New
                                                @elseif($ot->status == 'Pending')
                                                <i class="fa fa-dot-circle-o text-info"></i> Pending
                                                @elseif($ot->status == 'Approved')
                                                <i class="fa fa-dot-circle-o text-success"></i> Approved
                                                @elseif($ot->status == 'Rejected')
                                                <i class="fa fa-dot-circle-o text-danger"></i> Rejected
                                                @else
                                                <i class="fa fa-dot-circle-o"></i> Unknown
                                                @endif
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="#"><i
                                                        class="fa fa-dot-circle-o text-info"></i>
                                                    Pending</a>

                                                <form id="otapprove-form-{{ $ot->id }}"
                                                    action="{{ route('ot.approveadmin', $ot->id) }}" method="POST"
                                                    style="display:inline;">
                                                    @csrf
                                                    <button type="button" class="dropdown-item otapprove-button"
                                                        data-ot-id="{{ $ot->id }}">
                                                        <i class="fa fa-dot-circle-o text-success"></i> Approved
                                                    </button>
                                                </form>
                                                <form id="otdecline-form-{{ $ot->id }}"
                                                    action="{{ route('ot.rejectadmin', $ot->id) }}" method="POST"
                                                    style="display:inline;">
                                                    @csrf
                                                    <button type="button" class="dropdown-item otdecline-button"
                                                        data-ot-id="{{ $ot->id }}">
                                                        <i class="fa fa-dot-circle-o text-danger"></i> Rejected
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>

                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ url('admin/overtime') }}">View all overtime requests.</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 d-flex">
            <div class="card card-table flex-fill">
                <div class="card-header">
                    <h3 class="card-title mb-0">Attendance Requests <span
                            class="badge bg-inverse-danger ml-2">{{ $attendanceRequest->count() }}</span></h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table custom-table mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Date</th>
                                    <th>Total Hours</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($attendanceRequest->take(5) as $req)
                                <tr>
                                    <td>
                                        <h2 class="table-avatar">
                                            <a href="#" class="avatar">
                                                @if ($req->user->image)
                                                <img src="{{ asset('images/' . $req->user->image) }}"
                                                    alt="Profile Image" />
                                                @else
                                                <img src="{{
                                                        asset('images/default.png')
                                                    }}" alt="Profile Image" />
                                                @endif</a>
                                            <a href="{{ url('emp/profile/') }}">{{ $req->user->fName }}
                                                {{ $req->user->lName }}
                                                <span>{{ $req->user->position }}</span></a>
                                        </h2>
                                    </td>
                                    <td>{{ $req->date }}</td>
                                    <td>{{ $req->timeTotal }}</td>
                                    <td class="text-center">
                                        <div class="dropdown action-label">
                                            <a class="btn btn-white btn-sm btn-rounded dropdown-toggle" href="#"
                                                data-toggle="dropdown" aria-expanded="false">
                                                @if($req->status_code == 'New')
                                                <i class="fa fa-dot-circle-o text-purple"></i> New
                                                @elseif($req->status_code == 'Pending')
                                                <i class="fa fa-dot-circle-o text-info"></i> Pending
                                                @elseif($req->status_code == 'Approved')
                                                <i class="fa fa-dot-circle-o text-success"></i> Approved
                                                @elseif($req->status_code == 'Declined')
                                                <i class="fa fa-dot-circle-o text-danger"></i> Declined
                                                @else
                                                <i class="fa fa-dot-circle-o"></i> Unknown
                                                @endif
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="#"><i
                                                        class="fa fa-dot-circle-o text-info"></i>
                                                    Pending</a>

                                                <form id="attapprove-form-{{ $req->id }}"
                                                    action="{{ route('admin.approveAttendance', $req->id) }}"
                                                    method="POST" style="display:inline;">
                                                    @csrf
                                                    <button type="button" class="dropdown-item attapprove-button"
                                                        data-attendance-id="{{ $req->id }}">
                                                        <i class="fa fa-dot-circle-o text-success"></i> Approved
                                                    </button>
                                                </form>
                                                <form id="attdecline-form-{{ $req->id }}"
                                                    action="{{ route('admin.declineAttendance', $req->id ) }}"
                                                    method="POST" style="display:inline;">
                                                    @csrf
                                                    <button type="button" class="dropdown-item attdecline-button"
                                                        data-attendance-id="{{ $req->id }}">
                                                        <i class="fa fa-dot-circle-o text-danger"></i> Declined
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ url('admin/attendance') }}">View all attendance requests.</a>
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
<!-- Attendance Request -->

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var approveButtons = document.querySelectorAll('.attapprove-button');
        var declineButtons = document.querySelectorAll('.attdecline-button');

        approveButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                var attId = button.getAttribute('data-attendance-id');
                confirmAttApproval(attId);
            });
        });

        declineButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                var attId = button.getAttribute('data-attendance-id');
                confirmAttDecline(attId);
            });
        });
    });

    function confirmAttApproval(attId) {
        var form = document.getElementById('attapprove-form-' + attId);
        var confirmAction = confirm("Are you sure you want to approve this request?");
        if (confirmAction) {
            form.submit();
        }
    }

    function confirmAttDecline(attId) {
        var form = document.getElementById('attdecline-form-' + attId);
        var confirmAction = confirm("Are you sure you want to decline this request?");
        if (confirmAction) {
            form.submit();
        }
    }

</script>


<!-- OT -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var otApproveButtons = document.querySelectorAll('.otapprove-button');
        var otDeclineButtons = document.querySelectorAll('.otdecline-button');

        otApproveButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                var otId = button.getAttribute('data-ot-id');
                confirmOtApproval(otId);
            });
        });

        otDeclineButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                var otId = button.getAttribute('data-ot-id');
                confirmOtDecline(otId);
            });
        });
    });

    function confirmOtApproval(otId) {
        var form = document.getElementById('otapprove-form-' + otId);
        var confirmAction = confirm("Are you sure you want to approve this OT request?");
        if (confirmAction) {
            form.submit();
        }
    }

    function confirmOtDecline(otId) {
        var form = document.getElementById('otdecline-form-' + otId);
        var confirmAction = confirm("Are you sure you want to decline this OT request?");
        if (confirmAction) {
            form.submit();
        }
    }

</script>


<!-- Leave -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var approveButtons = document.querySelectorAll('.approve-button');
        var declineButtons = document.querySelectorAll('.decline-button');

        approveButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                var leaveId = button.getAttribute('data-leave-id');
                confirmApproval(leaveId);
            });
        });

        declineButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                var leaveId = button.getAttribute('data-leave-id');
                confirmDecline(leaveId);
            });
        });
    });

    function confirmApproval(leaveId) {
        var form = document.getElementById('approve-form-' + leaveId);
        var confirmAction = confirm("Are you sure you want to approve this leave request?");
        if (confirmAction) {
            form.submit();
        }
    }

    function confirmDecline(leaveId) {
        var form = document.getElementById('decline-form-' + leaveId);
        var confirmAction = confirm("Are you sure you want to decline this leave request?");
        if (confirmAction) {
            form.submit();
        }
    }

</script>

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

<script>
    function initializeApproveButtons() {
        const approveButtons = document.querySelectorAll('.approve-button');

        approveButtons.forEach(button => {
            button.addEventListener('click', function () {
                const leaveId = this.getAttribute('data-leave-id');
                const form = document.getElementById('approve-form-' + leaveId);
                const loader = document.getElementById('global-loader'); // Use global loader

                // Show the loader
                loader.style.display = 'flex';

                // Submit the form after showing the loader
                form.submit();
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initializeApproveButtons(); // Initialize on page load

        // If using AJAX for searching
        document.getElementById('search-button').addEventListener('click', function () {
            // Perform the search...

            // After updating the DOM with new leave requests, reinitialize the approve buttons
            initializeApproveButtons();
        });
    });

</script>

@endsection
