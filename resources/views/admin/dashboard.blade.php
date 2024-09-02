@extends('layouts.master') @section('title', 'One JAF')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')

<div class="content container-fluid">
    <!-- ATTENDANCE -->
    <div class="row">
        <div class="col-md-12">
            <div class="welcome-box">
                <div class="welcome-img">
                    <img alt="" src="{{ asset('images/' . Auth::user()->image) }}">
                </div>
                <div class="welcome-det">
                    <h3>Hi, {{ Auth::user()->name }}</h3>
                    <p>{{ date('l, j F Y') }}</p>
                </div>
            </div>
        </div>
    </div>


    <!-- Card stats -->
    <div class="row g-6 mb-6">
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card shadow border-0">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <span class="h6 font-semibold text-muted text-sm d-block mb-2">Client</span>
                            <span class="h3 font-bold mb-0">100</span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-tertiary text-white text-lg rounded-circle">
                                <i class="bi bi-credit-card"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card shadow border-0">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <span class="h6 font-semibold text-muted text-sm d-block mb-2">Employees</span>
                            <span class="h3 font-bold mb-0">{{ $totalUsers }}</span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-primary text-white text-lg rounded-circle">
                                <i class="bi bi-people"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card shadow border-0">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <span class="h6 font-semibold text-muted text-sm d-block mb-2">Attendance</span>
                            <span class="h3 font-bold mb-0">{{ $todayLoginCount }}</span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-info text-white text-lg rounded-circle">
                                <i class="bi bi-clock-history"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card shadow border-0">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <span class="h6 font-semibold text-muted text-sm d-block mb-2">Leave Today</span>
                            <span
                                class="h3 font-bold mb-0">{{ $vacationLeaveCountToday + $sickLeaveCountToday + $birthdayLeaveCountToday + $unpaidLeaveCountToday }}</span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-warning text-white text-lg rounded-circle">
                                <i class="bi bi-minecart-loaded"></i>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="mt-2 mb-0 text-sm">
                            <span class="badge badge-pill bg-soft-success text-success me-2">
                                <i class="bi bi-arrow-up me-1"></i>10%
                            </span>
                            <span class="text-nowrap text-xs text-muted">Since last month</span>
                        </div> -->
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-md-4">
            <div class="card punch-status">
                <div class="card-body">
                    <h5 class="card-title">
                        Timesheet
                        <small class="text-muted" id="clock"></small>
                    </h5>
                    <div class="punch-det">
                        @if (session('success'))
                        <div class="alert alert-success">
                            {{ session("success") }}
                        </div>
                        @elseif (session('error'))
                        <div class="alert alert-danger">
                            {{ session("error") }}
                        </div>
                        @else

                        <p>Welcome to One JAF!</p>
                        @endif
                    </div>
                    <div class="punch-info">
                        <div class="punch-hours">
                            @if($latest && $latest->date ===
                            now()->format('Y-m-d'))
                            <span>{{ $latest->timeTotal }}</span>
                            @else
                            <span>Total Time</span>
                            @endif
                        </div>
                    </div>

                    <!-- Attendance -->

                    <!-- Time In -->
                    <div class="punch-btn-container">
                        <form action="{{ url('admin/dashboard') }}" method="POST">
                            @csrf
                            <div class="punch-btn-section">
                                <button type="submit" class="btn btn-primary punch-btn">
                                    Clock In
                                </button>
                            </div>
                        </form>
                        <!-- Time Out -->

                        <div class="punch-btn-section">
                            <button type="submit" class="btn btn-outline-danger punch-btn" data-toggle="modal"
                                data-target="#exampleModal">
                                Clock Out
                            </button>
                        </div>
                    </div>

                    <!-- Break Buttons -->


                    <div class="row">
                        <div class="col-md-6 col-6 text-center">
                            <form action="{{ url('admin/dashboard/breakin') }}" method="POST">
                                @csrf @method('PUT')
                                <div class="stats-box">
                                    <button class="breakOut">
                                        Start Break
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-6 col-6 text-center">
                            <form action="{{ url('admin/dashboard/breakout') }}" method="POST">
                                @csrf @method('PUT')
                                <div class="stats-box">
                                    <button class="breakOut" data-toggle="modal" data-target="#exampleModalCenter">
                                        End Break
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">
                                Warning!
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to time out?
                        </div>
                        <div class="modal-footer">
                            <form action="{{ url('admin/dashboard/') }}" method="POST">
                                @csrf @method('PUT')
                                <button type="submit" class="btn btn-primary">
                                    Yes
                                </button>
                            </form>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                No
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal -->
        </div>
        <div class="col-md-4">
            <div class="card att-statistics">
                <div class="card-body">
                    <h5 class="card-title">Admin Info</h5>
                    <div class="stats-list">
                        <div class="stats-info">
                            <p>
                                Name
                                <strong>{{ Auth::user()->name }}</strong>
                            </p>
                        </div>
                        <div class="stats-info">
                            <p>
                                Employee ID
                                <strong>{{ Auth::user()->empNumber }}</strong>
                            </p>
                        </div>
                        <div class="stats-info">
                            <p>
                                Email
                                <strong>{{ Auth::user()->email }}</strong>
                            </p>
                        </div>
                        <div class="stats-info">
                            <p>
                                Position
                                <strong>{{ Auth::user()->position }}</strong>
                            </p>
                        </div>
                        <div class="stats-info">
                            <p>
                                Reporting to
                                @if ($supervisor === 'Management')

                                <strong>Management</strong>
                                @elseif ($supervisor)
                                <strong>{{ $supervisor->name }}</strong>
                                @else
                                <strong>No supervisor assigned for this department.</strong>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card recent-activity">
                <div class="card-body">
                    <h5 class="card-title">Today's Activity</h5>
                    <ul class="res-activity-list">
                        @if($latest && $latest->date === now()->format('Y-m-d'))
                        @if($latest->timeIn)
                        <li>
                            <p class="mb-0">Punch In at</p>
                            <p class="res-activity-time">
                                <i class="fa fa-clock-o"></i>
                                {{ $latest->timeIn }}
                            </p>
                        </li>
                        @endif @if($latest->breakIn)
                        <li>
                            <p class="mb-0">Break Out at</p>
                            <p class="res-activity-time">
                                <i class="fa fa-clock-o"></i>
                                {{ $latest->breakIn }}
                            </p>
                        </li>
                        @endif @if($latest->breakOut)
                        <li>
                            <p class="mb-0">Break In at</p>
                            <p class="res-activity-time">
                                <i class="fa fa-clock-o"></i>
                                {{ $latest->breakOut }}
                            </p>
                        </li>
                        @endif @if($latest->timeOut)
                        <li>
                            <p class="mb-0">Punch Out at</p>
                            <p class="res-activity-time">
                                <i class="fa fa-clock-o"></i>
                                {{ $latest->timeOut }}
                            </p>
                        </li>
                        @endif @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!--/ATTENDANCE -->

    @php
    $currentMonth = date('n'); // Current month as a number (1-12)
    $currentYear = date('Y'); // Current year
    $selectedMonth = old('month', request('month', $currentMonth));
    $selectedYear = old('year', request('year', $currentYear));
    @endphp

    <!-- Search Filter -->
    <form method="GET" action="{{ route('admin.index') }}">
        <div class="row filter-row">
            <div class="col-sm-6 col-md-2">
                <div class="form-group form-focus">
                    <input type="text" class="form-control floating" name="employee_name">
                    <label class="focus-label">Employee Name</label>
                </div>
            </div>
            <div class="col-sm-6 col-md-2">
                <div class="form-group form-focus select-focus">
                    <select class="select floating" name="department">
                        <option value="">- </option>
                        @foreach($departments as $dept)
                        <option value="{{ $dept->department }}"
                            {{ $dept->department == $departments ? 'selected' : '' }}>
                            {{ $dept->department }}
                        </option>
                        @endforeach
                    </select>
                    <label class="focus-label">Select Department</label>
                </div>
            </div>
            <div class="col-sm-6 col-md-2">
                <div class="form-group form-focus select-focus">
                    <select class="select floating" name="month">
                        <option value="">-</option>
                        @for($i = 1; $i <= 12; $i++) <option value="{{ $i }}"
                            {{ $i == $selectedMonth ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                            </option>
                            @endfor
                    </select>
                    <label class="focus-label">Select Month</label>
                </div>
            </div>
            <div class="col-sm-6 col-md-2">
                <div class="form-group form-focus select-focus">
                    <select class="select floating" name="year">
                        <option value="">-</option>
                        @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                        <option value="{{ $i }}" {{ $i == $selectedYear ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select> <label class="focus-label">Select Year</label>
                </div>
            </div>
            <div class="col-sm-6 col-md-4">
                <button type="submit" class="btn btn-primary btn-block"> Search </button>
            </div>
        </div>
    </form>
    <!-- /Search Filter -->

    <!-- CALENDAR RECORD -->

    <div class="row">
        <div class="col-lg-12">
            <div class="table-responsive">
                <!-- Display Attendance Table -->
                <table class="table table-striped custom-table table-nowrap mb-0">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            @for($i = 1; $i <= 31; $i++) <th>{{ $i }}</th>
                                @endfor
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>
                                <h2 class="table-avatar">
                                    <a href="#" class="avatar">
                                        @if ($user->image)
                                        <img src="{{ asset('images/' . $user->image) }}" alt="Profile Image" />
                                        @else
                                        <img src="{{
                                                asset('images/default.png')
                                            }}" alt="Profile Image" />
                                        @endif</a>
                                    <a href="#">{{ $user->name }}
                                        <span>{{ $user->department }}</span></a>
                                </h2>
                            </td>
                            @for($i = 1; $i <= 31; $i++) @php $attendance=$user->employeeAttendance->firstWhere('date',
                                $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($i, 2, '0',
                                STR_PAD_LEFT));
                                @endphp
                                <td>
                                    @if($attendance)
                                    <a href="javascript:void(0);" data-toggle="modal" data-target="#attendance_info"
                                        data-employee="{{ $user->name }}" data-date="{{ $attendance->date }}"
                                        data-timein="{{ $attendance->timeIn }}"
                                        data-timeout="{{ $attendance->timeOut }}"
                                        data-breakin="{{ $attendance->breakIn }}"
                                        data-breakend="{{ $attendance->breakEnd }}"
                                        data-breakout="{{ $attendance->breakOut }}"
                                        data-timetotal="{{ $attendance->timeTotal }}">
                                        <i class="fa fa-check text-success"></i>
                                    </a>
                                    @else
                                    <i class="fa fa-close text-danger"></i>
                                    @endif
                                </td>
                                @endfor
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- /Display Attendance Table -->
            </div>
        </div>
    </div>
</div>
<!-- /Page Content -->

<!-- Attendance Modal -->
<div class="modal custom-modal fade" id="attendance_info" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Attendance Info</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card punch-status">
                            <div class="card-body">
                                <h5 class="card-title">Timesheet <small class="text-muted"></small></h5>
                                <div class="punch-det">
                                    <h6>Punch In at</h6>
                                    <p></p>
                                </div>
                                <div class="punch-info">
                                    <div class="punch-hours">
                                        <span id="time-total"></span>
                                    </div>
                                </div>
                                <div class="punch-det">
                                    <h6>Punch Out at</h6>
                                    <p></p>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card recent-activity">
                            <div class="card-body">
                                <h5 class="card-title">Activity</h5>
                                <ul class="res-activity-list">
                                    <!-- Activities will be dynamically inserted here -->
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- /Attendance Modal -->
<!-- /Calendar Record -->


<!-- Employee Tabs -->

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title"></h4>
                    <ul class="nav nav-tabs nav-tabs-top">
                        <li class="nav-item"><a class="nav-link active" href="#top-tab1" data-toggle="tab">Employement
                                Record</a></li>
                        <li class="nav-item"><a class="nav-link" href="#top-tab2" data-toggle="tab">Salary Record</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane show active" id="top-tab1">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">Employment Record</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-nowrap">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Hired Date</th>
                                                        <th>Job Title</th>
                                                        <th>Department</th>
                                                        <th>Location</th>
                                                    </tr>
                                                </thead>
                                                @foreach ($record as $rec)
                                                <tbody>
                                                    <tr>
                                                        <td>{{ $rec->name }}</td>
                                                        <td>{{ $rec->hiredDate }}</td>
                                                        <td>{{ $rec->jobTitle }}</td>
                                                        <td>{{ $rec->department }}</td>
                                                        <td>{{ $rec->location }}</td>
                                                    </tr>
                                                </tbody>
                                                @endforeach
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="top-tab2">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">Salary Record</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-nowrap">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Annual Salary</th>
                                                        <th>Salary Frequency Monthly</th>
                                                        <th>Salary Rate</th>
                                                        <th>Currency</th>
                                                        <th>Proposal Reason</th>
                                                    </tr>
                                                </thead>
                                                @foreach ($salrecord as $sal)
                                                <tbody>
                                                    <tr>
                                                        <td>{{ $sal->name }}</td>
                                                        <td>{{ $sal->annSalary }}</td>
                                                        <td>{{ $sal->salFreqMonthly }}</td>
                                                        <td>{{ $sal->salRate }}</td>
                                                        <td>{{ $sal->currency }}</td>
                                                        <td>{{ $sal->proposalReason }}</td>
                                                    </tr>
                                                </tbody>
                                                @endforeach
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- /Employee Tabs -->
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
