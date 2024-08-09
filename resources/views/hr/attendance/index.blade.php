@extends('layouts.hrmaster') @section('title', 'Attendance') @section('content')

<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Attendance</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="index.html">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Attendance</li>
                </ul>
            </div>
            <div class="col-auto float-right ml-auto">
                <div class="view-icons">
                    <a href="{{ url('hr/attendance') }}" class="grid-view btn btn-link"><i class="fa fa-th"></i></a>
                    <a href="{{ url('hr/attendance/tableview') }}" class="list-view btn btn-link active"><i
                            class="fa fa-bars"></i></a>
                </div>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    @php
    $currentMonth = date('n'); // Current month as a number (1-12)
    $currentYear = date('Y'); // Current year
    $selectedMonth = old('month', request('month', $currentMonth));
    $selectedYear = old('year', request('year', $currentYear));
    @endphp

    <!-- Search Filter -->
    <form method="GET" action="{{ route('attendance.index') }}">
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

    <div class="row">
        <div class="col-lg-12">
            <div class="table-responsive">
                <!-- Display Attendance Table -->
                <table class="table table-hover table-nowrap custom-table table-nowrap mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th class="sticky-column">Employee</th>
                            @for($i = 1; $i <= 31; $i++) <th>{{ $i }}</th>
                                @endfor
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td class="sticky-column">
                                <h2 class="table-avatar">
                                    <a href="{{ url('hr/employee/edit'.$user->id) }}" class="avatar">
                                        @if ($user->image)
                                        <img src="{{ asset('images/' . $user->image) }}" alt="Profile Image" />
                                        @else
                                        <img src="{{ asset('images/default.png') }}" alt="Profile Image" />
                                        @endif
                                    </a>
                                    <a href="{{ url('hr/employee/edit/'.$user->id) }}">
                                        @if ($user->fName
                                        || $user->lName)
                                        {{ $user->fName }} {{ $user->lName }}
                                        @else
                                        {{ $user->name }}
                                        @endif
                                        <span>{{ $user->department }}</span>
                                    </a>
                                </h2>
                            </td>
                            @for($i = 1; $i <= 31; $i++) @php $attendance=$user->employeeAttendance->firstWhere(
                                'date',
                                $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($i, 2, '0',
                                STR_PAD_LEFT)
                                );
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

@endsection

@section('scripts')

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
